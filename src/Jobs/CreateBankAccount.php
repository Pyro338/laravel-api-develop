<?php

namespace Gamebetr\Api\Jobs;

use Carbon\Carbon;
use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\GlobalAuth\Models\Domain;
use Gamebetr\Api\Exceptions\UnknownDomain;
use Gamebetr\Api\Facades\Bank\Bank;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CreateBankAccount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Domain id.
     * @var int
     */
    protected $domainId;

    /**
     * Bank id.
     * @var string
     */
    protected $bankId;

    /**
     * Player id.
     * @var int
     */
    protected $playerId;

    /**
     * Balance.
     * @var float
     */
    protected $balance;

    /**
     * Name.
     * @var string
     */
    protected $name;

    /**
     * Description.
     * @var string
     */
    protected $description;

    /**
     * Class constructor.
     * @param int $domainId
     * @param string $bankId
     * @param int $playerId
     * @param float $balance
     * @param string $name
     * @param string $description
     * @return void
     */
    public function __construct(int $domainId, string $bankId, int $playerId, float $balance = 0, string $name = '', string $description = '')
    {
        $this->domainId = $domainId;
        $this->bankId = $bankId;
        $this->playerId = $playerId;
        $this->balance = $balance;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        if (!$domain = Domain::find($this->domainId)) {
            throw new UnknownDomain();
        }
        GlobalAuth::setDomain($domain);
        $accounts = Cache::remember('player_accounts_'.$this->playerId, Carbon::now()->addMinutes(30), function() {
            return Bank::getAccounts([
                'filter' => [
                    'player-id' => $this->playerId,
                    'bank' => $this->bankId,
                ],
            ]);
        });
        if (empty($accounts['data'])) {
            Bank::createAccount($this->bankId, $this->playerId, $this->balance, $this->name, $this->description);
        }
    }
}
