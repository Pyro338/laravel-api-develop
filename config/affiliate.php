<?php

return [
    'debug' => true,
    'log' => 'affiliate',
    'defaults' => [
        'default' => 'here',
    ],
    'domain_settings' => [
        1000001 => [
            'default_bank_id' => 139,
            'handlers' => [
                'casino_tag_vocabulary_id' => '\Gamebetr\Affiliate\Services\Process@casinoProcess',
                'sports_tag_vocabulary_id' => '\Playbetr\Affiliate\AffiliateProcess@sportsProcess',
            ],
        ],
        1000008 => [
            'default_bank_id' => 139,
            'handlers' => [
                5 => '\Playbetr\Affiliate\AffiliateProcess@diceProcess',
                6 => '\Playbetr\Affiliate\AffiliateProcess@casinoProcess',
                7 => '\Playbetr\Affiliate\AffiliateProcess@sportsProcess',
            ],
        ],
    ],
];
