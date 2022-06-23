<?php

namespace Gamebetr\Api\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameCenterConfirmationEvent
{
    use Dispatchable, SerializesModels;

    /**
     * Game Center Reservation.
     * @var /stdClass
     */
    public $gameCenterReservation;

    /**
     * Class constructor.
     * @var /stdClass $gameCenterReservation
     * @return void
     */
    public function __construct($gameCenterReservation)
    {
        $this->gameCenterReservation = $gameCenterReservation;
    }
}
