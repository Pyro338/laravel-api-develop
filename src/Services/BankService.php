<?php

namespace Gamebetr\Api\Services;

use Carbon\Carbon;
use DBD\GlobalAuth\Facades\GlobalAuth;
use Gamebetr\Api\Facades\Paybetr;
use Gamebetr\Api\Facades\User;
use Gamebetr\Api\Jobs\CreateBankAccount;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class BankService extends AbstractService
{
    /**
     * Special Bank Account Types
     * @const array
     */
    const SPECIAL_BANK_ACCOUNT_TYPES = [
        'status_points',
        'betback',
        'lossback',
        'lossback_paid',
        'affiliate',
        'winloss',
        'migration',
    ];

    /**
     * Get currencies.
     * @param array $query
     */
    public function getCurrencies(array $query = [])
    {
        return $this->request('GET', 'currencies?'.http_build_query($query), [], true, 60);
    }

    /**
     * Get currency by symbol.
     * @param string $symbol
     */
    public function getCurrencyBySymbol(string $symbol)
    {
        return $this->getCurrencies([
            'filter' => [
                'display-unit' => $symbol,
            ]
        ])['data'][0] ?? null;
    }

    /**
     * Get currency.
     * @param string $uuid
     * @param array $query
     */
    public function getCurrency(string $uuid, array $query = [])
    {
        return $this->request('GET', 'currencies/'.$uuid.'?'.http_build_query($query), [], true, 60);
    }

    /**
     * Get banks.
     * @param array $query
     */
    public function getBanks(array $query = [])
    {
        return $this->request('GET', 'banks?'.http_build_query($query), [], true, 5);
    }

    /**
     * Get bank.
     * @param string $uuid
     * @param array $query
     */
    public function getBank(string $uuid, array $query = [])
    {
        return $this->request('GET', 'banks/'.$uuid.'?'.http_build_query($query), [], true, 60);
    }

    /**
     * Get bank currency.
     * @param string $uuid
     * @param array $query
     */
    public function getBankCurrency(string $uuid, array $query = [])
    {
        return $this->request('GET', 'banks/'.$uuid.'/currency?'.http_build_query($query), [], true, 60);
    }

    /**
     * Get bank display currency.
     * @param string $uuid
     * @param array $query
     */
    public function getBankDisplayCurrency(string $uuid, array $query = [])
    {
        return $this->request('GET', 'banks/'.$uuid.'/display-currency?'.http_build_query($query), [], true, 60);
    }

    /**
     * Create bank.
     * @param string $name
     * @param string $description
     * @param bool $hidden
     * @param bool $transferable
     * @param string $currencyUuid
     * @param string $displayCurrencyUuid
     * @param array $tags
     * @param bool $relaxed
     */
    public function createBank(
        string $name = null,
        string $description = null,
        bool $hidden = false,
        bool $transferable = false,
        bool $relaxed = false,
        bool $playable = true,
        string $currency,
        string $displayCurrency,
        string $depositCurrency = null,
        array $tags = []
    ) {
        $currency = $this->getCurrencyBySymbol($currency);
        if (isset($currency['data'])) {
            $currency = $currency['data'];
        }
        $displayCurrency = $this->getCurrencyBySymbol($displayCurrency);
        if (isset($displayCurrency['data'])) {
            $displayCurrency = $displayCurrency['data'];
        }
        if ($depositCurrency) {
            $depositCurrency = $this->getCurrencyBySymbol($depositCurrency);
            if (isset($depositCurrency['data'])) {
                $depositCurrency = $depositCurrency['data'];
            }
        }
        $data = [
            'data' => [
                'type' => 'banks',
                'attributes' => [
                    'name' => $name,
                    'description' => $description,
                    'hidden' => $hidden,
                    'transferable' => $transferable,
                    'relaxed-balances' => $relaxed,
                    'playable' => $playable,
                    'tags' => $this->formatTags($tags),
                ],
                'relationships' => [
                    'currency' => [
                        'data' => [
                            'type' => 'currencies',
                            'id' => $currency['id'],
                        ],
                    ],
                    'display-currency' => [
                        'data' => [
                            'type' => 'currencies',
                            'id' => $displayCurrency['id'],
                        ],
                    ],
                ],
            ],
        ];
        if($depositCurrency) {
            $data['data']['relationships']['deposit-currency'] = [
                'data' => [
                    'type' => 'currencies',
                    'id' => $depositCurrency['id'],
                ],
            ];
        }
        $bank = $this->request('POST', 'banks', $data);
        if (isset($bank['data'])) {
            $bank = $bank['data'];
        }

        return $bank;
    }

    /**
     * Get win loss
     *
     * @param string $accountUuid
     *   The bank account UUID.
     * @param array $args
     *   Optional filter, paging, and sort arguments for this report.
     *
     * @return array
     *   The response.
     */
    public function getAccountWinLoss(string $accountUuid, array $args = []): array
    {
        return $this->reportWinLossHandler(sprintf('bank-accounts/%s/reports/win-loss', $accountUuid), $args);
    }

    /**
     * Run a Win Loss report for an account grouped by tag(s).
     *
     * @param string $accountUuid
     *   The bank account UUID.
     * @param array $args
     *   Optional filter, paging, and sort arguments for this report.
     *
     * @return array
     *   The response.
     */
    public function reportAccountWinLossByTags(string $accountUuid, array $args = []): array
    {
        return $this->reportWinLossHandler(sprintf('bank-accounts/%s/reports/win-loss-tags', $accountUuid), $args);
    }

    /**
     * Run a Win Loss report for the bank.
     *
     * @param string $bankUuid
     *   The bank UUID.
     * @param array $args
     *   Optional filter, paging, and sort arguments for this report.
     *
     * @return array
     *   The response.
     */
    public function reportWinLoss(string $bankUuid, array $args = []): array
    {
        return $this->reportWinLossHandler(sprintf('banks/%s/reports/win-loss', $bankUuid), $args, $bankUuid);
    }

    /**
     * Run a top users Win Loss report grouped by tag.
     *
     * @param string $bankUuid
     *   The bank UUID.
     * @param array $args
     *   Optional filter, paging, and sort arguments for this report.
     *
     * @return array
     *   The response.
     */
    public function reportTopUsersByTag(string $bankUuid, array $args = []): array
    {
        return $this->reportWinLossHandler(sprintf('banks/%s/reports/top-users-tag', $bankUuid), $args, $bankUuid);
    }

    /**
     * Run a Win Loss report grouped by tag(s).
     *
     * @param string $bankUuid
     *   The bank UUID.
     * @param array $args
     *   Optional filter, paging, and sort arguments for this report.
     *
     * @return array
     *   The response.
     */
    public function reportWinLossByTags(string $bankUuid, array $args = []): array
    {
        return $this->reportWinLossHandler(sprintf('banks/%s/reports/win-loss-tags', $bankUuid), $args, $bankUuid);
    }

    /**
     * Run a Win Loss report grouped by tag(s).
     *
     * @param string $bankUuid
     *   The bank UUID.
     * @param array $args
     *   Optional filter, paging, and sort arguments for this report.
     *
     * @return array
     *   The response.
     */
    public function reportWinLossByTagsAggregate(string $bankUuid, array $args = []): array
    {
        return $this->reportWinLossHandler(
            sprintf('banks/%s/reports/win-loss-tags-aggregate', $bankUuid),
            $args,
            $bankUuid
        );
    }

    /**
     * General handler for calling WinLoss reports.
     *
     * @param string $endpoint
     *   The endpoint to hit.
     * @param Collection|array|iterable $args
     *   Parameters to pass along.
     * @param string|null $uuid
     *   The UUID to use when forcing a UUID filter.
     * @param string $uuidKey
     *   The UUID key to use when forcing a UUID filter. Defaults to filtering
     *   on banks.
     *
     * @return array
     *   The request result.
     */
    protected function reportWinLossHandler(string $endpoint, iterable $args, string $uuid = null, string $uuidKey = 'bank-uuid'): array
    {
        if (!empty($uuid)) {
            $filters = $args['filter'] ?? [];
            $filters[$uuidKey ?? 'bank-uuid'] = $uuid;
            $args['filter'] = $filters;
        }

        $args = $args instanceof Collection ?
            $args->toArray() :
            $args;

        return $this->request('GET', $endpoint.'?'.http_build_query($args));
    }

    /**
     * Get accounts.
     * @param array $query
     */
    public function getAccounts(array $query = [])
    {
        return $this->request('GET', 'bank-accounts?'.http_build_query($query));
    }

    /**
     * Get account.
     * @param string $uuid
     * @param array $query
     */
    public function getAccount(string $uuid, array $query = [])
    {
        return $this->request('GET', 'bank-accounts/'.$uuid.'?'.http_build_query($query));
    }

    /**
     * Get status points account.
     * @param int $playerId
     */
    public function getStatusPointsAccount(int $playerId)
    {
        return $this->getAccountByPlayerIdAndType($playerId, 'status_points');
    }

    /**
     * Get affililate account.
     * @param int $playerId
     */
    public function getAffiliateAccount(int $playerId)
    {
        return $this->getAccountByPlayerIdAndType($playerId, 'affiliate');
    }

    /**
     * Get betback account.
     * @param int $playerId
     */
    public function getBetbackAccount(int $playerId)
    {
        return $this->getAccountByPlayerIdAndType($playerId, 'betback');
    }

    /**
     * Get lossback account.
     * @param int $playerId
     */
    public function getLossbackAccount(int $playerId)
    {
        return $this->getAccountByPlayerIdAndType($playerId, 'lossback');
    }

    /**
     * Get lossback paid account.
     * @param int $playerId
     */
    public function getLossbackPaidAccount(int $playerId)
    {
        return $this->getAccountByPlayerIdAndType($playerId, 'lossback_paid');
    }

    /**
     * Get winloss account.
     * @param int $playerId
     */
    public function getWinlossAccount(int $playerId)
    {
        return $this->getAccountByPlayerIdAndType($playerId, 'winloss');
    }

    /**
     * Get account by player id and type
     * @param int $playerId
     * @param string $type
     */
    public function getAccountByPlayerIdAndType(int $playerId, string $type)
    {
        if (in_array($type, self::SPECIAL_BANK_ACCOUNT_TYPES)) {
            return $this->getSpecialAccountType($playerId, $type);
        }
        return $this->getAccountByCurrency($playerId, $type);
    }

    /**
     * Get special account type
     * @param int $playerId
     * @param string $type
     */
    public function getSpecialAccountType(int $playerId, string $type)
    {
        if (!in_array($type, self::SPECIAL_BANK_ACCOUNT_TYPES)) {
            abort(422, 'Invalid account type');
        }
        if (!$domain = GlobalAuth::getDomain()) {
            abort(422, 'Unknown domain');
        }
        if (!$bankUuid = $domain->variable($type.'_bank_uuid')) {
            abort(422, 'Unknown '.$type.' bank');
        }
        if (empty($accounts = $this->request('GET', 'bank-accounts?'.http_build_query(['filter' => ['player-id' => $playerId, 'bank' => $bankUuid]]))['data'])) {
            $bank = $this->getBank($bankUuid);
            CreateBankAccount::dispatch($domain->id, $bankUuid, $playerId, 0, $bank['data']['attributes']['name'], $bank['data']['attributes']['description']);
            abort(422, 'Unknown '.$type.' account');
        }

        return $accounts[0];
    }

    /**
     * Get account by currency
     * @param int $playerId
     * @param string $currency
     */
    public function getAccountByCurrency(int $playerId, string $currency)
    {
        if (in_array($currency, self::SPECIAL_BANK_ACCOUNT_TYPES)) {
            abort(422, 'Invalid account currency');
        }
        if(empty($accounts = $this->request('GET', 'bank-accounts?'.http_build_query(['filter' => ['player-id' => $playerId, 'bank.playable' => 1, 'bank.display-currency.display-unit' => $currency]]))['data'])) {
            $bank = $this->getBankByCurrency($currency);
            CreateBankAccount::dispatch(GlobalAuth::getDomain()->id, $bank['id'], $playerId, 0, $bank['attributes']['name'], $bank['attributes']['description']);
            abort(422, 'Unknown '.$currency.' account');
        }

        return $accounts[0];
    }

    /**
     * Get bank by currency
     * @param string $currency
     */
    public function getBankByCurrency(string $currency) {
        if(empty($banks = $this->request('GET', 'banks?'.http_build_query(['filter' => ['playable' => 1, 'display-currency.name' => $currency]]))['data'])) {
            abort(404, 'Unknown bank');
        }

        return $banks[0];
    }

    /**
     * Get account bank.
     * @param string $accountUuid
     */
    public function getAccountBank(string $accountUuid)
    {
        $bank = Cache::remember('account_bank_'.$accountUuid, Carbon::now()->addMinutes(15), function () use ($accountUuid) {
            return $this->request('GET', 'bank-accounts/'.$accountUuid.'/bank');
        });
        if (isset($bank['data'])) {
            $bank = $bank['data'];
        }

        return $bank;
    }

    /**
     * Create account.
     * @param string $bankUuid
     * @param int $userId
     * @param float $balance
     * @param string $name
     * @param string $description
     * @param array $tags
     */
    public function createAccount(string $bankUuid, int $userId, float $balance = 0, string $name = '', string $description = '', array $tags = [])
    {
        $account = $this->request('POST', 'bank-accounts', [
            'data' => [
                'type' => 'bank-accounts',
                'attributes' => [
                    'name' => $name,
                    'description' => $description,
                    'player-id' => $userId,
                    'balance' => $balance,
                    'tags' => $this->formatTags($tags),
                ],
                'relationships' => [
                    'bank' => [
                        'data' => [
                            'type' => 'banks',
                            'id' => $bankUuid,
                        ],
                    ],
                ],
            ],
        ]);
        if(isset($account['data'])) {
            $account = $account['data'];
        }

        return $account;
    }

    /**
     * Get transactions.
     */
    public function getTransactions(array $query = [])
    {
        return $this->request('GET', 'transactions?'.http_build_query($query));
    }

    /**
     * Get holds.
     */
    public function getHolds(array $query = [])
    {
        return $this->request('GET', 'holds?'.http_build_query($query));
    }

    /**
     * Get transaction.
     * @param string $uuid
     */
    public function getTransaction(string $uuid, array $query = [])
    {
        return $this->request('GET', 'transactions/'.$uuid.'?'.http_build_query($query));
    }

    /**
     * Get transaction notes.
     * @param string $uuid
     * @param array $query
     * @return array
     */
    public function getTransactionNotes(string $uuid, array $query = [])
    {
        return $this->request('GET', 'transactions/'.$uuid.'/notes?'.http_build_query($query));
    }

    /**
     * Get hold.
     * @param string $uuid
     */
    public function getHold(string $uuid, array $query = [])
    {
        return $this->request('GET', 'holds/'.$uuid.'?'.http_build_query($query));
    }

    /**
     * Create a transaction.
     *
     * @param  string  $accountUuid
     *   The account UUID.
     * @param  float  $amount
     *   The transaction amount.
     * @param  string  $type
     *   The transaction type.
     * @param  string  $service
     *   The transaction origin service.
     * @param  array  $optional
     *   A keyed array of optional transaction attributes: tags, parent, note, serviceCategory
     *
     * @return array|string
     */
    public function createTransaction(string $accountUuid, float $amount, string $type, string $service, array $optional = [])
    {
        // <editor-fold desc="Humans make mistakes">
        $allowed_optionals = [
            'serviceCategory',
            'tags',
            'parent',
            'note',
        ];

        // A little error hardening to help prevent typos or wrong key names going unnoticed and creating transactions
        // with the wrong or missing data.
        if (array_diff(array_keys($optional), $allowed_optionals)) {
            throw new \RuntimeException('Invalid optional transaction attribute');
        }
        // </editor-fold>

        $data = [
            'data' => [
                'type' => 'transactions',
                'attributes' => [
                    'amount' => $amount,
                    'service' => $service,
                    'transaction-type' => $type,
                ],
                'relationships' => [
                    'bank-account' => [
                        'data' => [
                            'type' => 'bank-accounts',
                            'id' => $accountUuid,
                        ],
                    ],
                ],
            ],
        ];

        // <editor-fold desc="Optional attribute assignment">
        if (!empty($optional['serviceCategory'])) {
            $data['data']['attributes']['service-category'] = $optional['serviceCategory'];
        }

        if (!empty($optional['tags'])) {
            $data['data']['attributes']['tags'] = $this->formatTags($optional['tags']);
        }

        if (!empty($optional['parent'])) {
            $data['data']['relationships']['parent'] = [
                'data' => [
                    'type' => 'transactions',
                    'id' => $optional['parent'],
                ],
            ];
        }
        // </editor-fold>

        $transaction = $this->request('POST', 'transactions', $data);

        if (!empty($optional['note']) && is_string($optional['note'])) {
            $this->addNote('transactions', $transaction['data']['id'], $optional['note']);
        }

        return $transaction;
    }

    /**
     * Add note.
     * @param string $type
     * @param string $uuid
     * @param string $note
     */
    public function addNote(string $type, string $uuid, string $note) {
        $data = [
            'data' => [
                'type' => 'notes',
                'attributes' => [
                    'note' => $note,
                ],
                'relationships' => [
                    'noteable' => [
                        'data' => [
                            'type' => $type,
                            'id' => $uuid,
                        ],
                    ],
                ],
            ],
        ];

        return $this->request('POST', 'notes', $data);
    }

    /**
     * Create hold.
     *
     * @param  string  $accountUuid
     *   The account UUID.
     * @param  float  $amount
     *   The transaction amount.
     * @param  string  $type
     *   The transaction type.
     * @param  string  $service
     *   The transaction origin service.
     * @param  array  $optional
     *   A keyed array of optional transaction attributes: tags, serviceCategory
     *
     * @return array|string
     */
    public function createHold(string $accountUuid, float $amount, string $type, string $service, array $optional = [])
    {
        $data = [
            'data' => [
                'type' => 'holds',
                'attributes' => [
                    'amount' => $amount,
                    'service' => $service,
                    'transaction-type' => $type,
                ],
                'relationships' => [
                    'bank-account' => [
                        'data' => [
                            'type' => 'bank-accounts',
                            'id' => $accountUuid,
                        ],
                    ],
                ],
            ],
        ];

        // <editor-fold desc="Optional attribute assignment">
        if (!empty($optional['serviceCategory'])) {
            $data['data']['attributes']['service-category'] = $optional['serviceCategory'];
        }

        if (!empty($optional['tags'])) {
            $data['data']['attributes']['tags'] = $this->formatTags($optional['tags']);
        }
        // </editor-fold>

        return $this->request('POST', 'holds', $data);
    }

    /**
     * Confirm hold.
     * @param string $holdUuid
     */
    public function confirmHold(string $holdUuid)
    {
        return $this->request('PATCH', 'holds/'.$holdUuid, [
            'data' => [
                'type' => 'holds',
                'id' => $holdUuid,
                'attributes' => [
                    'status' => 'CONFIRMED',
                ],
            ],
        ]);
    }

    /**
     * Cancel hold.
     * @param string $holdUuid
     */
    public function cancelHold(string $holdUuid)
    {
        return $this->request('PATCH', 'holds/'.$holdUuid, [
            'data' => [
                'type' => 'holds',
                'id' => $holdUuid,
                'attributes' => [
                    'status' => 'RELEASED',
                ],
            ],
        ]);
    }

    /**
     * Make account primary.
     * @param string $currency.
     */
    public function makeAccountPrimary(int $playerId, string $currency)
    {
        $account = $this->getAccountByCurrency($playerId, $currency);
        return $this->request('PATCH', 'bank-accounts/'.$account['id'], [
            'data' => [
                'type' => 'bank-accounts',
                'id' => $account['id'],
                'attributes' => [
                    'primary' => 1,
                ],
            ],
        ]);
    }

    /**
     * Get primary account.
     * @param int $playerId
     */
    public function getPrimaryAccount(int $playerId)
    {
        $filter = [
            'filter' => [
                'player-id' => $playerId,
                'primary' => 1,
            ]
        ];
        $accounts = $this->request('GET', 'bank-accounts?'.http_build_query($filter));
        if(isset($accounts['data'])) {
            $accounts = $accounts['data'];
        }
        if(empty($accounts)) {
            $baseCurrency = GlobalAuth::getDomain()->variable('base_currency', 'USDT');
            return $this->getAccountByPlayerIdAndType($playerId, $baseCurrency);
        }

        return $accounts[0];
    }

    /**
     * Get bank currency symbol
     * @param string $bankUuid
     * @return string
     */
    public function getBankCurrencySymbol(string $bankUuid)
    {
        return Cache::remember('bank_currency_'.$bankUuid, Carbon::now()->addMinutes(15), function () use ($bankUuid) {
            $currency = $this->request('GET', 'banks/'.$bankUuid.'/display-currency');
            return $currency['data']['attributes']['display-unit'];
        });
    }

    /**
     * Get account by bank and player id.
     * @param string $bankUuid
     * @param int $playerId
     * @return array
     */
    public function getAccountByBankAndPlayerId(string $bankUuid, int $playerId)
    {
        return Cache::remember('bank_account_'.$bankUuid.'_'.$playerId, Carbon::now()->addMinutes(5), function () use ($bankUuid, $playerId) {
            return $this->request('GET', 'banks/'.$bankUuid.'/bank-accounts?filter[player-id]='.$playerId);
        });
    }

    /**
     * Transfer
     * @param Authenticatable $user
     * @param string $from
     * @param string $to
     * @param float $amount
     */
    public function transfer(Authenticatable $user, string $from, string $to, float $amount): array
    {
        if ($amount <= 0) {
            abort(422, 'Amount must be greater than zero');
        }

        [$fromAccountId, $fromCurrency] = $this->transferGetValidatedCurrency((int)$user->id, $from, true);
        [$toAccountId, $toCurrency] = $this->transferGetValidatedCurrency((int)$user->id, $to, false);

        $newAmount = $fromCurrency == $toCurrency ?
            $amount :
            Paybetr::convertCurrency($fromCurrency, $toCurrency, $amount)->attributes->price->price;

        abort_if(empty($newAmount), JsonResponse::HTTP_INTERNAL_SERVER_ERROR, 'Unable to convert currency');

        return [
            $this->createTransaction($fromAccountId, $amount * -1, 'transfer', 'bank')['data'],
            $this->createTransaction($toAccountId, $newAmount, 'transfer', 'bank')['data'],
        ];
    }

    /**
     * p2pTransfer
     * @param Authenticatable $user
     * @param int $toUser
     * @param string $currency
     * @param float $amount
     */
    public function p2pTransfer(Authenticatable $fromUser, int $toUser, string $currency, float $amount, $note = null): array
    {
        if ($amount <= 0) {
            abort(422, 'Amount must be greater than zero');
        }

        $fromAccountId = $this->transferGetValidatedP2P((int)$fromUser->id, $currency, true);
        $toAccountId = $this->transferGetValidatedP2P($toUser, $currency, false);

        return [
            $this->createTransaction($fromAccountId, $amount * -1, 'p2p_transfer', 'bank')['data'],
            $this->createTransaction($toAccountId, $amount, 'p2p_transfer', 'bank', ['note' => $note])['data'],
        ];
    }

    /**
     * Get account currency.
     * @param string $uuid
     * @return string
     */
    public function getAccountCurrency(string $uuid) {
        if(!$account = $this->request('GET', 'bank-accounts/'.$uuid, [], true, 5)) {
            abort(404, 'Unkown account');
        }
        if(isset($account['data'])) {
            $account = $account['data'];
        }

        return $account['attributes']['bank']['display-currency']['display-unit'];
    }

    /**
     * Format tags.
     *
     * Allow tags to be passed as an associative array with the indexes
     * being the vocabulary and the value being a comma separated
     * list of tags.
     *
     * @param array $tags
     *   The associative tag array.
     *
     * @return array
     *   The jsonapi formatted tag array.
     */
    public function formatTags(array $tags): array
    {
        $formattedTags = [];
        foreach($tags as $vocabulary => $tag) {
            if(is_array($tag) && isset($tag['vocabulary']) && isset($tag['name']) ) {
                $vocabulary = $tag['vocabulary'];
                $tag = $tag['name'];
            }
            foreach(explode(',', $tag) as $value) {
                if(empty($value)) {
                    continue;
                }
                $formattedTags[] = [
                    'vocabulary' => $vocabulary,
                    'name' => $value,
                ];
            }
        }

        return $formattedTags;
    }

    /**
     * {@inheritdoc}
     */
    public function getServiceDomainKey(): string
    {
        return 'bank';
    }

    /**
     * {@inheritdoc}
     */
    public function request(string $method, string $endpoint, array $parameters = [], bool $cache = false, int $cacheMinutes = 0, array &$errors = null) {
        $response = parent::request($method, $endpoint, $parameters, $cache, $cacheMinutes);
        return User::injectPlayer($response);
    }

    /**
     * Get the account currency after validating dependencies.
     *
     * @param int $userId
     *   The active user ID.
     * @param string $accountId
     *   The account ID.
     * @param bool $isOrigin
     *   If this account is being used as the source for a transfer.
     *
     * @return array
     *   An array of the account ID and currency.
     */
    protected function transferGetValidatedCurrency(int $userId, string $accountId, bool $isOrigin = false): array
    {
        $found = $this->getAccountByPlayerIdAndType($userId, $accountId);

        abort_if(empty($found['id']), JsonResponse::HTTP_BAD_REQUEST, 'Missing account id for '.($isOrigin ? 'origin' : 'destination'));
        abort_if(empty($found['attributes']['player-id']), JsonResponse::HTTP_BAD_REQUEST, 'Missing player-id for'.($isOrigin ? 'origin' : 'destination'));
        abort_unless((int)$found['attributes']['player-id'] === $userId, JsonResponse::HTTP_BAD_REQUEST, 'Invalid account for '.($isOrigin ? 'origin' : 'destination'));

        $account = $found;

        $found = $this->getAccountBank($account['id']);

        abort_if(empty($found['id']), JsonResponse::HTTP_BAD_REQUEST, 'Invalid bank for '.($isOrigin ? 'origin' : 'destination'));
        abort_if($isOrigin && empty($found['attributes']['transferable']), JsonResponse::HTTP_BAD_REQUEST, 'Transfers cannot be made from account');

        $bank = $found;

        $found = $this->getBankCurrencySymbol($bank['id']);
        abort_if(empty($found), JsonResponse::HTTP_BAD_REQUEST, 'Invalid currency for '.($isOrigin ? 'origin' : 'destination'));

        $currency = $found;

        return [$account['id'], $currency];
    }

    /**
     * Get the account currency after validating dependencies.
     *
     * @param int $userId
     *   The active user ID.
     * @param string $accountId
     *   The account ID.
     * @param bool $isOrigin
     *   If this account is being used as the source for a transfer.
     *
     * @return array
     *   An array of the account ID and currency.
     */
    protected function transferGetValidatedP2P(int $userId, string $accountId, bool $isOrigin = false): string
    {
        $found = $this->getAccountByPlayerIdAndType($userId, $accountId);

        abort_if(empty($found['id']), JsonResponse::HTTP_BAD_REQUEST, 'Missing account id for '.($isOrigin ? 'origin' : 'destination'));
        abort_if(empty($found['attributes']['player-id']), JsonResponse::HTTP_BAD_REQUEST, 'Missing player-id for'.($isOrigin ? 'origin' : 'destination'));
        abort_unless((int)$found['attributes']['player-id'] === $userId, JsonResponse::HTTP_BAD_REQUEST, 'Invalid account for '.($isOrigin ? 'origin' : 'destination'));

        $account = $found;

        $found = $this->getAccountBank($account['id']);

        abort_if(empty($found['id']), JsonResponse::HTTP_BAD_REQUEST, 'Invalid bank for '.($isOrigin ? 'origin' : 'destination'));
        abort_if(empty($found['attributes']['playable']), JsonResponse::HTTP_BAD_REQUEST, 'Transfers cannot be made for' .($isOrigin ? 'origin' : 'destination'));

        $bank = $found;

        $found = $this->getBankCurrencySymbol($bank['id']);
        abort_if(empty($found), JsonResponse::HTTP_BAD_REQUEST, 'Invalid currency for '.($isOrigin ? 'origin' : 'destination'));

        return $account['id'];
    }
}
