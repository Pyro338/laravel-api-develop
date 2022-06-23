<?php

namespace Gamebetr\Api\Services;

use DBD\GlobalAuth\Facades\GlobalAuth;
use Exception;
use Gamebetr\Api\Events\GameCenterTransactionEvent;
use Gamebetr\Api\Facades\Bank\Bank;
use Gamebetr\Api\Facades\Paybetr;
use Gamebetr\Api\Models\GameCenterReservation;
use Gamebetr\Api\Models\GameCenterTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GameCenterService extends AbstractService
{
    /**
     * Get request currency.
     * @param Request $request
     * @return string
     */
    public function getRequestCurrency(Request $request)
    {
        if(isset($request->get('parameters')['currency'])) {
            return $request->get('parameters')['currency'];
        }

        return $this->getAccountCurrency($request);
    }

    /**
     * Get account currency.
     * @param Request $request
     * @return string
     */
    public function getAccountCurrency(Request $request)
    {
        return Bank::getAccountCurrency($request->get('external_id'));
    }

    /**
     * Get price.
     * @param string $from
     * @param string $to
     * @return float
     */
    public function getPrice(string $from, string $to)
    {
        return Paybetr::getPrice($from, $to);
    }

    /**
     * Process tags.
     * @param string $type - casino|sports
     * @param Request $request
     * @return array
     */
    public function processTags(string $type, Request $request)
    {
        if(!in_array($type, ['casino', 'sports'])) {
            abort(422, 'Impoper type');
        }
        $gameCenterTags = [];

        foreach((array) $request->get('tags') ?? [] as $tag) {
            $gameCenterTags[] = strtolower($tag);
        }
        $tags = [];
        if(isset($request->get('parameters')['currency'])) {
            $tags[] = [
                'vocabulary' => 'play-currency',
                'name' => strtolower($request->get('parameters')['currency']),
            ];
        }
        foreach(array_unique($gameCenterTags) as $tag) {
            $tags[] = [
                'vocabulary' => 'game-center',
                'name' => $tag,
            ];
        }

        return $tags;
    }

    /**
     * Process balance callback
     * @param Request $request
     * @return float
     */
    public function processBalanceCallback(Request $request)
    {
        $account = Bank::getAccount($request->get('external_id'));
        $balance = $account['data']['attributes']['available-balance'];

        return bcmul($balance, $this->getPrice($this->getAccountCurrency($request), $this->getRequestCurrency($request)), 8);
    }

    /**
     * Process transaction callback
     * @param Request $request
     * @return float
     */
    public function processTransactionCallback(Request $request)
    {
        $sport_games = ['nsoft-live', 'nsoft-prematch'];
        $category = in_array($request->get('game'), $sport_games, true) ? 'sports' : 'casino';
        $tags = $this->processTags($category, $request);

        $amount = $request->get('amount') ?? 0;

        $type = $amount > 0 ? 'win' : 'bet';

        $bankTransaction = Bank::createTransaction(
            $request->get('external_id'),
            bcmul($amount, $this->getPrice($this->getRequestCurrency($request), $this->getAccountCurrency($request)), 8),
            $type, 'game-center', [
                'serviceCategory' => $category,
                'tags' => $tags ?? [],
            ]
        );
        if(isset($bankTransaction['data'])) {
            $bankTransaction = $bankTransaction['data'];
        }
        $domain = GlobalAuth::getDomain();
        $gameCenterTransaction = GameCenterTransaction::create([
            'domain_id' => $domain->id,
            'game' => $request->get('game'),
            'game_session_id' => $request->get('game_session_id'),
            'game_transaction_id' => $request->get('transaction_id'),
            'account_id' => $request->get('external_id'),
            'bank_transaction_id' => $bankTransaction['id'],
            'amount' => $amount,
            'currency' => $this->getRequestCurrency($request),
        ]);
        GameCenterTransactionEvent::dispatch($gameCenterTransaction, $bankTransaction, $category, $tags);

        return bcmul($bankTransaction['attributes']['available-balance'], $this->getPrice($this->getAccountCurrency($request), $this->getRequestCurrency($request)), 8);
    }

    /**
     * Process reeserve callback
     * @param Request $request
     * @return float
     */
    public function processReserveCallback(Request $request)
    {
        $sport_games = ['nsoft-live', 'nsoft-prematch'];
        $category = in_array($request->get('game'), $sport_games, true) ? 'sports' : 'casino';
        $tags = $this->processTags($category, $request);
        $bankReservation = Bank::createHold(
            $request->get('external_id'),
            bcmul($request->get('amount'), $this->getPrice($this->getRequestCurrency($request), $this->getAccountCurrency($request)), 8),
            'bet',
            'game-center',
            [
                'serviceCategory' => $category,
                'tags' => $tags,
            ]
        );
        if(isset($bankReservation['data'])) {
            $bankReservation = $bankReservation['data'];
        }
        GameCenterReservation::create([
            'game_transaction_id' => $request->get('reservation_id'),
            'bank_reservation_id' => $bankReservation['id'],
        ]);

        return bcmul($bankReservation['attributes']['available-balance'], $this->getPrice($this->getAccountCurrency($request), $this->getRequestCurrency($request)), 8);
    }

    /**
     * Process confirm callback
     * @param Request $request
     * @return float
     */
    public function processConfirmCallback(Request $request)
    {
        $gameCenterReservation = GameCenterReservation::where('game_transaction_id', $request->get('reservation_id'))->first();
        abort_if(empty($gameCenterReservation), JsonResponse::HTTP_NOT_FOUND);
        if($request->get('game') == 'nsoft-live' || $request->get('game') == 'nsoft-prematch') {
            $type = 'sports';
            $tags = $this->processTags('sports', $request);
        } else {
            $type = 'casino';
            $tags = $this->processTags('casino', $request);
        }

        $bankReservation = Bank::confirmHold($gameCenterReservation->bank_reservation_id);
        $bankTransaction = Bank::getTransaction($bankReservation['data']['relationships']['transaction']['data']['id']);
        if(isset($bankTransaction['data'])) {
            $bankTransaction = $bankTransaction['data'];
        }
        //$gameCenterReservation->delete();
        $gameCenterTransaction = GameCenterTransaction::create([
            'domain_id' => GlobalAuth::getDomain()->id,
            'game' => $request->get('game'),
            'game_session_id' => $request->get('game_session_id'),
            'game_transaction_id' => $request->get('transaction_id'),
            'account_id' => $request->get('external_id'),
            'bank_transaction_id' => $bankReservation['data']['relationships']['transaction']['data']['id'],
            'amount' => $request->get('amount'),
        ]);
        GameCenterTransactionEvent::dispatch($gameCenterTransaction, $bankTransaction, $type, $tags);

        return bcmul($bankReservation['data']['attributes']['available-balance'], $this->getPrice($this->getAccountCurrency($request), $this->getRequestCurrency($request)), 8);
    }

    /**
     * Process cancel callback
     * @param Request $request
     * @return float
     */
    public function processCancelCallback(Request $request)
    {
        $gameCenterReservation = GameCenterReservation::where('game_transaction_id', $request->get('reservation_id'))
            ->first();
        abort_if(empty($gameCenterReservation), JsonResponse::HTTP_NOT_FOUND, 'Unknown reservation');

        $bankReservation = Bank::cancelHold($gameCenterReservation->bank_reservation_id);
        $gameCenterReservation->delete();

        return bcmul($bankReservation['data']['attributes']['available-balance'], $this->getPrice($this->getAccountCurrency($request), $this->getRequestCurrency($request)), 8);
    }

    /**
     * List providers.
     * @param $query
     */
    public function listProviders(array $query = [])
    {
        return $this->request('GET', 'provider/list?'.http_build_query($query), [], true, 5);
    }

    /**
     * Get provider.
     * @param string $uuid
     */
    public function getProvider($uuid, array $query = [])
    {
        return $this->request('GET', 'provider/'.$uuid.'?'.http_build_query($query), [], true, 5);
    }

    /**
     * List games.
     * @param $query
     */
    public function listGames($query = [])
    {
        return $this->request('GET', 'game/list?'.http_build_query($query), [], true, 5);
    }

    /**
     * Get game.
     * @param string $uuid
     */
    public function getGame($uuid, array $query = [])
    {
        return $this->request('GET', 'game/'.$uuid.'?'.http_build_query($query), [], true, 5);
    }

    /**
     * Update game.
     *
     * @param string $uuid
     * @param array $attributes
     */
    public function updateGame($uuid, array $attributes = [])
    {
        return $this->request('PUT', 'game/'.$uuid, $attributes);
    }

    /**
     * List transactions.
     * @param $query
     */
    public function listTransactions($query = null)
    {
        $url = 'game/transactions';
        if ($query) {
            $url .= '?'.urldecode($query);
        }
        return $this->request('GET', $url);
    }

    /**
     * Get transaction.
     * @param string $uuid
     */
    public function getTransaction($uuid, array $query = [])
    {
        return $this->request('GET', 'game/transactions/'.$uuid.'?'.http_build_query($query));
    }

    /**
     * List tickets.
     * @param array $query
     */
    public function listTickets(array $query = []) {
        return $this->request('GET', 'nsoft/tickets?'.http_build_query($query));
    }

    /**
     * List bets.
     * @param array $query
     */
    public function listBets(array $query = []) {
        return $this->request('GET', 'nsoft/bets?'.http_build_query($query));
    }

    /**
     * Get ticket.
     * @param string $ticketHash
     */
    public function getTicket(string $ticketHash, array $query = []) {
        $include = [];
        if(isset($query['include'])) {
            $include = explode(',', $query['include']);
        }
        if(!in_array('gameSession', $include)) {
            $include[] = 'gameSession';
        }
        $query['include'] = implode(',', $include);
        $ticket = $this->request('GET', 'nsoft/tickets/'.$ticketHash.'?'.http_build_query($query), [], true, 5);
        abort_if(empty($ticket), JsonResponse::HTTP_NOT_FOUND);

        $ticket = json_decode($ticket, false);
        abort_if(empty($ticket->data->related->gameSession->attributes->external_id), JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'Missing object properties');

        $account = Bank::request('GET', 'bank-accounts/'.$ticket->data->related->gameSession->attributes->external_id, [], true, 60);
        abort_if(empty($account), JsonResponse::HTTP_NOT_FOUND);

        if(isset($account['data'])) {
            $account = $account['data'] ?? [];
        }

        abort_if(empty($account['attributes']['bank']['display-currency']['display-unit']), JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'Missing account data');

        $ticket->data->attributes->currency = $account['attributes']['bank']['display-currency']['display-unit'];
        unset($ticket->data->related->gameSession);

        return $ticket;
    }

    /**
     * List bonuses.
     * @param array $query
     */
    public function listBonuses(array $query = []) {
        return $this->request('GET', 'bonus?'.http_build_query($query));
    }

    /**
     * Create bonus.
     */
    public function createBonus($domain_id, $player_id, $user_id, $external_id, $balance, $target_balance, $games)
    {
        $parameters = [
            'domain_id' => $domain_id,
            'player_id' => $player_id,
            'user_id' => $user_id,
            'external_id' => $external_id,
            'balance' => $balance,
            'target_balance' => $target_balance,
            'games' => $games,
        ];

        return json_decode($this->request('POST', 'bonus', $parameters));
    }

    /**
     * Launch game.
     * @param string $uuid
     * @param bool $anonymous
     * @param string|null $currency
     * @param bool $private
     * @param string|null $language
     * @param string|null $country
     * @return mixed
     * @throws Exception
     */
    public function launchGame(
        $uuid,
        bool $anonymous = true,
        string $currency = null,
        bool $private = false,
        string $language = null,
        string $country = null,
        array $customParameters = [],
        array $tags = []
    ) {
        if (!$game = $this->getGame($uuid)) {
            abort(404, 'Unknown game');
        }
        $domain = GlobalAuth::getDomain();
        $game = json_decode($game);
        $accountUuid = null;
        if($anonymous) {
            return [
                'data' => [
                    'launch_url' => $domain->variable('game_center_launch_url').'/'.$game->data->attributes->launch_id,
                ],
            ];
        }
        if (!$anonymous) {
            $account = Bank::getPrimaryAccount(Auth::id());
            $accountUuid = $account['id'];
            $customParameters['currency'] = $currency ?? $account['attributes']['bank']['display-currency']['display-unit'];
        }
        $parameters = [
            'anonymous' => $anonymous,
            'game' => $game->data->attributes->launch_id,
            'domain_id' => $domain->id,
            'player_id' => Auth::id(),
            'external_id' => $accountUuid,
            'private' => $private,
            'language' => $language,
            'min_bet' => $domain->variable('min_bet', .01),
            'max_bet' => $domain->variable('max_bet', 10000),
            'parameters' => $customParameters,
            'tags' => $tags,
        ];

        $providersParameters = [
            'betsoft' => 'bank_id',
            'hub88' => 'operator_id',
            'igaming' => 'bank_id',
            'nsoft' => 'bank_id',
            'betradar' => 'bank_id',
            'friendsplay' => 'operator_id',
            'luckystreak' => 'operator_id',
            'softswiss' => 'operator_id',
        ];
        foreach ($providersParameters as $provider => $parameter) {
            if (!Str::startsWith($game->data->attributes->provider_id, $provider)) {
                continue;
            }

            $parameters['parameters'][$parameter] = $domain->variable($provider.'_'.$parameter);
            $parameters['min_bet'] = $domain->variable($provider.'_min_bet', $domain->variable('min_bet', .01));
            $parameters['max_bet'] = $domain->variable($provider.'_max_bet', $domain->variable('max_bet', 10000));

            if($provider === 'hub88') {
                if($anonymous) {
                    $parameters['country'] = 'XX';
                }else {
                    if ($country) {
                        $parameters['country'] = $country;
                    } elseif (!empty($_SERVER["HTTP_CF_IPCOUNTRY"])) {
                        $parameters['country'] = $_SERVER["HTTP_CF_IPCOUNTRY"];
                    } else {
                        abort(422, 'Unknown country');
                    }
                }
            }
        }

        $gameSession = json_decode($this->request('POST', 'game/prepare', $parameters));
        if (!empty($gameSession->error)) {
            abort(422, $gameSession->message);
        }
        $gameSession->data->launch_url = GlobalAuth::getDomain()->variable('game_center_launch_url').'?session_token='.$gameSession->data->attributes->session_token;
        return $gameSession;
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceDomainKey(): string
    {
        return 'game_center';
    }

    /**
     * {@inheritdoc}
     */
    protected function processResponse($response)
    {
        return $response;
    }

    /**
     * List bonus.
     */
    public function betsoftBonusInfo(array $query = [])
    {
      $url = 'betsoft/bonus/info';
      if ($query) {
        $url .= '?'.http_build_query($query);
      }
      return $this->request('GET', $url);
    }

    /**
     * Create bonus award.
     */
    public function betsoftBonusAward($domain_id, $player_id, $bank_id, $currency, $type, $amount, $multiplier, $games, $game_ids, $exp_date, $comment, $description)
    {
      $parameters = [
        'domain_id' => $domain_id,
        'player_id' => $player_id,
        'bank_id' => $bank_id,
        'currency' => $currency,
        'type' => $type,
        'amount' => $amount,
        'multiplier' => $multiplier,
        'games' => $games,
        'game_ids' => $game_ids,
        'exp_date' => $exp_date,
        'comment' => $comment,
        'description' => $description,
      ];
      return json_decode($this->request('POST', 'betsoft/bonus/award', $parameters));
    }

    /**
     * List fr bonus.
     */
    public function betsoftfrBonusInfo(array $query = [])
    {
      $url = 'betsoft/frbonus/info';
      if ($query) {
        $url .= '?'.http_build_query($query);
      }
      return $this->request('GET', $url);
    }

    /**
     * Create fr bonus award.
     */
    public function betsoftfrBonusAward($domain_id, $player_id, $bank_id, $currency, $rounds, $game_ids, $comment, $description, $start_time, $exp_time, $duration, $exp_hours, $table_round_chips)
    {
      $parameters = [
        'domain_id' => $domain_id,
        'player_id' => $player_id,
        'bank_id' => $bank_id,
        'currency' => $currency,
        'rounds' => $rounds,
        'game_ids' => $game_ids,
        'comment' => $comment,
        'description' => $description,
        'start_time' => $start_time,
        'exp_time' => $exp_time,
        'duration' => $duration,
        'exp_hours' => $exp_hours,
        'table_round_chips' => $table_round_chips,
      ];
      return json_decode($this->request('POST', 'betsoft/frbonus/award', $parameters));
    }

}
