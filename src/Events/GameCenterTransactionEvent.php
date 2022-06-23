<?php

namespace Gamebetr\Api\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameCenterTransactionEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Game Center Transaction.
     * @var /stdClass
     */
    public $gameCenterTransaction;

    /**
     * Bank Transaction.
     * @var array
     */
    public $bankTransaction;

    /**
     * Tags
     * @var array
     */
    public $tags;

    /**
     * Type
     * @var string
     */
    public $type;

    /**
     * Class constructor.
     * @var /stdClass $gameCenterTransaction
     * @return void
     */
    public function __construct($gameCenterTransaction, $bankTransaction, $type = 'casino', $tags = [])
    {
        $this->gameCenterTransaction = $gameCenterTransaction;
        if(isset($bankTransaction['data'])) {
            $bankTransaction = $bankTransaction['data'];
        }
        $this->bankTransaction = $bankTransaction;
        $this->type = $type;
        $this->tags = $tags;
    }
}
