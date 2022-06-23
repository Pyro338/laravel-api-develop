<?php

return [
    'debug' => false,
    'log' => 'vip',
    'domain_settings' => [
        1000001 => [
            'handlers' => [
                'casino_tag_vocabulary_id' => '\Gamebetr\Api\Services\VipProcessService@casinoProcess',
                'sports_tag_vocabulary_id' => '\Gamebetr\Api\Services\VipProcessService@sportsProcess',
            ],
            'vip_points_multipliers' => [
                'casino_tag_vocabulary_id' => 0.2,
                'sports_tag_vocabulary_id' => 0.1,
            ],
            'base_datetimes' => [
                'sports_tag_vocabulary_id' => '2019-01-22 00:00:00',
            ],
        ],
        1000008 => [
            //
        ],
    ],
    // 'propertySettings' => [
    //     2 => [
    //         'default_bank_id' => 35,
    //         'handlers' => [
    //             13 => '\Playbetr\Vip\VipProcess@diceProcess',
    //             10 => '\Playbetr\Vip\VipProcess@casinoProcess',
    //             11 => 'enabled',
    //         ],
    //         'status_points_multipliers' => [
    //             13 => 0.1,
    //             10 => 0.2,
    //             11 => 0.1,
    //         ]
    //     ],
    //     4 => [
    //         'default_bank_id' => 46,
    //         'handlers' => [
    //             22 => '\Playbetr\Vip\VipProcess@diceProcess',
    //             17 => '\Playbetr\Vip\VipProcess@casinoProcess',
    //         ],
    //         'status_points_multipliers' => [
    //             22 => 0.01,
    //             17 => 0.02,
    //         ]
    //     ],
    //     5 => [
    //         'default_bank_id' => 66,
    //         'handlers' => [
    //             5 => '\Playbetr\Vip\VipProcess@diceProcess',
    //             2 => '\Playbetr\Vip\VipProcess@casinoProcess',
    //             3 => 'enabled',
    //         ],
    //         'status_points_multipliers' => [
    //             5 => 0.025,
    //             2 => 0.05,
    //             3 => 0.10, // 0.05
    //         ],
    //         'base_datetimes' => [
    //             3 => '2019-01-22 00:00:00',
    //         ],
    //     ],
    //     12 => [
    //         'default_bank_id' => 89,
    //         'handlers' => [
    //             24 => '\Playbetr\Vip\VipProcess@diceProcess',
    //             23 => '\Playbetr\Vip\VipProcess@casinoProcess',
    //         ],
    //         'status_points_multipliers' => [
    //             24 => 0.01,
    //             23 => 0.02,
    //         ]
    //     ],
    //     14 => [
    //         'default_bank_id' => 98,
    //         'handlers' => [
    //             26 => '\Playbetr\Vip\VipProcess@diceProcess',
    //             25 => '\Playbetr\Vip\VipProcess@casinoProcess',
    //         ],
    //         'status_points_multipliers' => [
    //             26 => 0.01,
    //             25 => 0.02,
    //         ]
    //     ],
    //     29 => [
    //         'default_bank_id' => 114,
    //         'handlers' => [
    //             27 => '\Playbetr\Vip\VipProcess@casinoProcess',
    //         ],
    //         'status_points_multipliers' => [
    //             27 => 0.02,
    //         ]
    //     ],
    // ],
];
