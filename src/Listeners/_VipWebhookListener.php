<?php

namespace Gamebetr\Api\Listeners;

use Submtd\LaravelWebhooksClient\Events\WebhookEvent;
use Gamebetr\Api\Services\VipProcessService;

class VipWebhookListener
{

    public function handle(WebhookEvent $event)
    {

        if (config('playbetr.vip.debug')) {
            \Log::debug('-- vip webhook start --');
        }

        // only process ticket-updated (whole ticket, not the individual bets)
        if ($event->trigger != 'nsoft.ticket-updated') {
            return;
        }

        // process
        $data = $event->payload;
        VipProcess::newSportsProcess($data);

        if (config('playbetr.vip.debug')) {
            \Log::debug('-- vip webhook end --');
        }
        
        return;
    }
}
