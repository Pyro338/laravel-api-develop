<?php

namespace Gamebetr\Api\Listeners;

use Submtd\LaravelWebhooksClient\Events\WebhookEvent;
use Gamebetr\Api\Services\AffiliateProcessService;

class AffiliateWebhookListener
{

    public function handle(WebhookEvent $event)
    {
        
        // only process ticket-updated (whole ticket, not the individual bets)
        if ($event->trigger != 'nsoft.ticket-updated') {
            return;
        }

        // process
        $data = $event->payload;
        AffiliateProcess::newSportsProcess($data);
    }
}
