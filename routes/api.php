<?php

use Gamebetr\Api\Controllers\Bank\BankReports;
use Gamebetr\Api\Controllers\Bank\AccountReports;
use Gamebetr\Api\Controllers\Bank\GetAccount;
use Gamebetr\Api\Controllers\Leaderboard\LeaderboardReports;
use Gamebetr\Api\Middleware\GameCenterCallback;
use Gamebetr\Api\Middleware\UserAccess;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'api/v1',
    'middleware' => [
        'api',
    ],
], function () {

    /**
     * VIP ROUTES
     * -------------------------------------------------------------------------
     */
    Route::group([
        'prefix' => 'vip',
        'namespace' => 'Gamebetr\Api\Controllers\Vip',
    ], function () {
        /**
         * Public routes
         */
        Route::get('/', 'VipInfo')->name('api.vip.info');
        /**
         * Protected routes
         */
        Route::group([
            'middleware' => [
                'auth:api',
                UserAccess::class,
            ],
        ], function () {
            Route::get('user', 'VipUserInfo')->name('api.vip.userinfo');
        });
    });

    /**
     * AFFILIATE ROUTES
     * -------------------------------------------------------------------------
     */
    Route::group([
        'prefix' => 'affiliate',
        'namespace' => 'Gamebetr\Api\Controllers\Affiliate',
    ], function () {
        /**
         * Public routes
         */
        Route::get('/', 'AffiliateInfo')->name('api.affiliate.info');
        Route::post('clicks', 'CreateClick')->name('api.affiliate.createclick');
        Route::post('conversions', 'CreateConversion')->name('api.affiliate.createconversion');
        /**
         * Protected routes
         */
        Route::group([
            'middleware' => [
                'auth:api',
                UserAccess::class,
            ],
        ], function () {
            Route::get('clicks', 'ListClicks')->name('api.affiliate.listclicks');
            Route::get('conversions', 'ListConversions')->name('api.affiliate.listconversions');
            Route::get('user', 'AffiliateUserInfo')->name('api.affiliate.userinfo');
            Route::get('user/list', 'AffiliateUserList')->name('api.affiliate.userlist');
        });
    });

    /*
     * USER ROUTES
     * -------------------------------------------------------------------------
     */
    Route::group([
        'prefix' => 'user',
        'namespace' => 'Gamebetr\Api\Controllers\User',
    ], function () {
        /*
         * Public routes
         */
        Route::post('login', 'Login')->name('api.user.login');
        Route::post('login_2fa', 'Login2FA')->name('api.user.login_2fa');
        Route::match(['get', 'post'], 'password', 'Password')->name('api.user.password'); // TODO
        Route::post('register', 'Register')->name('api.user.register');
        Route::post('refresh', 'Refresh')->name('api.user.refresh');
        /*
         * Protected routes
         */
        Route::group([
            'middleware' => [
                'auth:api',
                UserAccess::class,
            ],
        ], function () {
            /*
            * Individual User Routes
            */
            Route::post('avatar', 'Avatar')->name('api.user.avatar');
            Route::delete('avatar', 'DeleteAvatar')->name('api.user.deleteavatar');
            Route::get('disable2fa', 'Disable2fa')->name('api.user.disable2fa');
            Route::get('enable2fa', 'Enable2fa')->name('api.user.enable2fa');
            Route::post('update', 'Update')->name('api.user.update');
            Route::get('/', 'User')->name('api.user');
            Route::get('vip', 'VipInfo')->name('api.user.vipinfo');
            Route::get('affiliate', 'AffiliateInfo')->name('api.user.affiliateinfo');
            Route::get('apitoken', 'CreateApiToken')->name('api.user.createapitoken');
            Route::get('variables', 'ListVariables')->name('api.user.listvariables');
            Route::post('variables', 'CreateVariable')->name('api.user.createvariable');
            Route::get('variables/{variable}', 'GetVariable')->name('api.user.getvariable');
            /*
             * Admin User Routes
             */
            Route::group([
                'middleware' => 'domainadmin',
            ], function () {
                Route::get('list', 'ListUsers')->name('api.user.listusers');
                Route::post('create', 'CreateUser')->name('api.user.createuser');
                Route::get('{uuid}', 'Find')->name('api.user.find');
                Route::post('{uuid}', 'AdminUpdate')->name('api.user.adminupdate');
            });
        });
    });

    /*
    * P2P ROUTES
    * -------------------------------------------------------------------------
    */
    Route::group([
        'prefix' => 'p2p',
        'middleware' => [
            'auth:api',
            UserAccess::class,
        ],
        'namespace' => 'Gamebetr\Api\Controllers\P2P',
    ], function () {
        Route::post('transfer', 'Transfer')->name('api.p2p.transfer');
    });

    /*
     * BANK ROUTES
     * -------------------------------------------------------------------------
     */
    Route::group([
        'prefix' => 'bank',
        'middleware' => [
            'auth:api',
            UserAccess::class,
        ],
        'namespace' => 'Gamebetr\Api\Controllers\Bank',
    ], function () {
        /**
         * Admin bank routes
         */
        Route::group([
            'middleware' => 'domainadmin',
        ], function () {
            Route::post('/', 'CreateBank')->name('api.bank.createbank');
            Route::post('managertransaction', 'ManagerTransaction')->name('api.bank.managertransaction');
        });

        /**
         * User bank routes
         */
        Route::group([
            'prefix' => 'account',
        ], function () {
            Route::get('/', 'ListAccounts')->name('api.bank.listaccounts');
            Route::get('{uuid}/get', [GetAccount::class, 'getByUuid']);
            Route::get('{type}', [GetAccount::class, 'getByType'])->name('api.bank.getaccount');
            Route::get('{type}/primary', 'MakeAccountPrimary')->name('api.bank.makeaccountprimary');
            Route::get('{type}/reports/win-loss/{start}/{end}', [AccountReports::class, 'winLoss'])->name('api.bank.account.winloss');
            Route::get('{type}/reports/win-loss-by-tags', [AccountReports::class, 'winLossByTags'])->name('api.bank.account.winlossbytags');
        });

        Route::group([
            'prefix' => '{bankUuid}/reports',
        ], function () {
            Route::get('win-loss', [BankReports::class, 'winLoss'])->name('api.bank.reports.winloss');
            Route::get('win-loss-top-by-tag', [BankReports::class, 'topUsersByTag'])->name('api.bank.reports.winlosstopbytag');
            Route::get('win-loss-by-tags', [BankReports::class, 'winLossByTags'])->name('api.bank.reports.winlossbytags');
            Route::get('win-loss-by-tags-aggregate', [BankReports::class, 'winLossByTagsAggregate'])->name('api.bank.reports.winlossbytagsaggregate');
        });

        Route::apiResource('transaction', 'TransactionController')->only(['index', 'show']);
        Route::get('transaction/{uuid}/notes', 'TransactionController@notes');
        Route::apiResource('tags', 'TagController')->only(['index', 'show']);

        Route::get('/', 'ListBanks')->name('api.bank.listbanks');
        Route::get('currency', 'ListCurrencies')->name('api.bank.listcurrencies');
        Route::get('currency/{symbol}', 'GetCurrency')->name('api.bank.getcurrency');
        Route::get('hold', 'ListHolds')->name('api.bank.listholds');
        Route::get('hold/{uuid}', 'GetHold')->name('api.bank.gethold');
        Route::post('transfer', 'Transfer')->name('api.bank.transfer');
        Route::get('{uuid}', 'GetBank')->name('api.bank.getbank');
    });

    Route::group([
        'prefix' => 'leaderboard',
        'namespace' => 'Gamebetr\Api\Controllers\Leaderboard',
    ], static function () {
        Route::group([
            'prefix' => 'reports',
        ], static function () {
            Route::get('top-bet', [LeaderboardReports::class, 'reportTopBet'])->name('api.leaderboard.top-bet');
            Route::get('most-bets', [LeaderboardReports::class, 'reportMostBets'])->name('api.leaderboard.most-bets');
        });
    });

    /*
     * PAYBETR ROUTES
     * -------------------------------------------------------------------------
     */
    Route::group([
        'prefix' => 'paybetr',
        'namespace' => 'Gamebetr\Api\Controllers\Paybetr',
    ], function () {
        /*
         * PAYBETR PUBLIC ROUTES
         */
        Route::post('callback', 'Callback')->name('api.paybetr.callback');
        Route::get('confirmwithdrawal/{uuid}', 'ConfirmWithdrawal')->name('api.paybetr.confirmwithdrawal');
        Route::get('cancelwithdrawal/{uuid}', 'CancelWithdrawal')->name('api.paybetr.cancelwithdrawal');
        /*
         * PAYBETR PROTECTED ROUTES
         */
        Route::group([
            'middleware' => [
                'auth:api',
                UserAccess::class,
            ]
        ], function () {
            Route::get('balance', 'ListBalances')->name('api.paybetr.listbalances');
            Route::get('balance/{symbol}', 'ShowBalance')->name('api.paybetr.showbalance');
            Route::get('currency', 'ListCurrencies')->name('api.paybetr.listcurrencies');
            Route::get('currency/{symbol}/convert/{to}/{amount?}', 'ConvertCurrency')->name('api.paybetr.convertcurrency');
            Route::get('currency/{symbol}', 'GetCurrency')->name('api.paybetr.getcurrency');
            Route::get('address/{symbol?}', 'ListAddresses')->name('api.paybetr.listaddresses');
            Route::post('address', 'CreateAddress')->name('api.paybetr.createaddress');
            Route::get('withdrawal', 'ListWithdrawals')->name('api.paybetr.listwithdrawals');
            Route::get('withdrawal/{uuid}', 'GetWithdrawal')->name('api.paybetr.getwithdrawal');
            Route::post('withdrawal', 'CreateWithdrawal')->name('api.paybetr.createwithdrawal');
            Route::get('transaction', 'ListTransactions')->name('api.paybetr.listtransactions');
            Route::get('transaction/{uuid}', 'GetTransaction')->name('api.paybetr.gettransaction');
            Route::post('moonpay', 'Moonpay')->name('api.paybetr.moonpay');
        });
    });

    /*
     * GAMECENTER ROUTES
     * -------------------------------------------------------------------------
     */
    Route::group([
        'prefix' => 'gamecenter',
        'namespace' => 'Gamebetr\Api\Controllers\GameCenter',
    ], function () {
        /*
         * Callback Routes
         */
        Route::group([
            'middleware' => GameCenterCallback::class,
        ], function () {
            Route::post('callback/balance', 'CallbackBalance')->name('api.gamecenter.callback.balance');
            Route::post('callback/transaction', 'CallbackTransaction')->name('api.gamecenter.callback.transaction');
            Route::post('callback/reserve', 'CallbackReserve')->name('api.gamecenter.callback.reserve');
            Route::post('callback/confirm', 'CallbackConfirm')->name('api.gamecenter.callback.confirm');
            Route::post('callback/cancel', 'CallbackCancel')->name('api.gamecenter.callback.cancel');
        });
        /*
         * Public Routes
         */
        Route::get('game', 'ListGames')->name('api.gamecenter.listgames');
        Route::get('game/{uuid}', 'GetGame')->name('api.gamecenter.getgame');
        Route::match(['get', 'post'], 'game/{uuid}/launch', 'LaunchGame')->name('api.gamecenter.launchgame');
        Route::get('provider', 'ListProviders')->name('api.gamecenter.listproviders');
        Route::get('provider/{uuid}', 'GetProvider')->name('api.gamecenter.getprovider');
        Route::get('ticket/{ticketHash}', 'GetTicket')->name('api.gamecenter.getticket');
        /*
        * GameCenter Routes
        */
        Route::group([
            'middleware' => [
                'auth:api',
                UserAccess::class,
            ],
        ], function () {
            Route::get('transaction', 'ListTransactions')->name('api.gamecenter.listtransactions');
            Route::get('transaction/{uuid}', 'GetTransaction')->name('api.gamecenter.gettransaction');
            Route::get('ticket', 'ListTickets')->name('api.gamecenter.listtickets');
            Route::get('bet', 'ListBets')->name('api.gamecenter.listbets');

            // Bonus endpoint
            Route::post('betsoft/bonus/award', 'BetsoftBonusAward')->name('api.gamecenter.betsoftbonusaward');
            Route::get('betsoft/bonus/info', 'BetsoftBonusInfo')->name('api.gamecenter.betsoftbonusinfo');
            Route::post('betsoft/frbonus/award', 'BetsoftfrBonusAward')->name('api.gamecenter.betsoftfrbonusaward');
            Route::get('betsoft/frbonus/info', 'BetsoftfrBonusInfo')->name('api.gamecenter.betsoftfrbonusinfo');

            Route::get('bonus', 'ListBonuses')->name('api.gamecenter.listbonuses');
            Route::post('bonus', 'CreateBonus')->name('api.gamecenter.createbonus');

            // Prize endpoint
            Route::post('prize/award', 'AwardPrize')->name('api.gamecenter.prizeaward');
        });
    });

    /*
     * AFFILIATE ROUTES
     * -------------------------------------------------------------------------
     */
    Route::group([
        'prefix' => 'affiliate',
        'middleware' => [
            'auth:api',
            UserAccess::class,
        ],
        'namespace' => 'Gamebetr\Api\Controllers\Affiliate',
    ], function () {
        Route::get('users', 'ListUsers')->name('api.affiliate.listusers');
        //Route::post('users', 'CreateUser')->name('api.affiliate.createuser');
        Route::get('media', 'ListMedia')->name('api.affiliate.listmedia');
        Route::post('media/group', 'CreateMediaGroup')->name('api.affiliate.createmediagroup');
        Route::post('media/item', 'CreateMediaItem')->name('api.affiliate.createmediaitem');

        Route::get('config', 'ListConfig')->name('api.affiliate.listconfig');
        Route::get('rates', 'ListRates')->name('api.affiliate.listrates');
        Route::get('tiers', 'ListTiers')->name('api.affiliate.listtiers');
    });

    /*
     * VIP ROUTES
     * -------------------------------------------------------------------------
     */
    Route::group([
        'prefix' => 'vip',
        'middleware' => [
            'auth:api',
            UserAccess::class,
        ],
        'namespace' => 'Gamebetr\Api\Controllers\Vip',
    ], function () {
        Route::get('config', 'ListConfig')->name('api.vip.listconfig');
        Route::get('levels', 'ListLevels')->name('api.vip.listlevels');
        Route::get('rates', 'ListRates')->name('api.vip.listrates');
    });

    /*
     * SUPPORT TICKET ROUTES
     * -------------------------------------------------------------------------
     */
    Route::group([
        'prefix' => 'support',
        'middleware' => [
            'auth:api',
            UserAccess::class,
        ],
        'namespace' => 'Gamebetr\Api\Controllers\Support',
    ], function () {
        Route::get('ticket', 'ListTickets')->name('api.support.listtickets');
        Route::get('ticket/{uuid}', 'GetTicket')->name('api.support.getticket');
        Route::put('ticket/{uuid}', 'UpdateTicket')->name('api.support.updateticket');
        Route::post('ticket', 'CreateTicket')->name('api.support.createticket');
    });

    /*
     * ADMIN ROUTES
     * -------------------------------------------------------------------------
     */
    Route::group([
        'prefix' => 'admin',
        'middleware' => [
            'auth:api',
            UserAccess::class,
            'domainadmin',
        ],
    ], function () {
        Route::group([
            'namespace' => 'Gamebetr\Api\Controllers\Admin',
        ], function () {
            Route::get('domains', 'ListDomains');
        });

        /*
         * ADMIN BANK ROUTES
         * ---------------------------------------------------------------------
         */
        Route::group([
            'prefix' => 'bank',
            'namespace' => 'Gamebetr\Api\Controllers\Admin\Bank',
        ], function () {
            Route::get('/', 'ListBanks')->name('api.admin.bank.listbanks');
            Route::post('/', 'CreateBank')->name('api.admin.bank.createbank');
            Route::get('{uuid}', 'GetBank')->name('api.admin.bank.getbank');
            Route::put('{uuid}', 'UpdateBank')->name('api.admin.bank.updatebank');
            Route::delete('{uuid}', 'DeleteBank')->name('api.admin.bank.deletebank');
            Route::get('account', 'ListAccounts')->name('api.admin.bank.listaccoutns');
            Route::post('account', 'CreateAccount')->name('api.admin.bank.createaccount');
            Route::put('account/{uuid}', 'UpdateAccount')->name('api.admin.bank.updateaccount');
            Route::delete('account/{uuid}', 'DeleteAccount')->name('api.admin.bank.deleteaccount');
            Route::get('transaction', 'ListTransactions')->name('api.admin.bank.listtransactions');
            Route::post('transaction', 'CreateTransaction')->name('api.admin.bank.createtransaction');
        });

        /*
         * ADMIN PAYBETR ROUTES
         * ---------------------------------------------------------------------
         */
        Route::group([
            'prefix' => 'paybetr',
            'namespace' => 'Gamebetr\Api\Controllers\Admin\Paybetr',
        ], function () {
            Route::get('address', 'ListAddresses')->name('api.admin.paybetr.listaddresses');
            Route::get('address/{address}', 'GetAddress')->name('api.admin.paybetr.getaddress');
            Route::put('address/{address}', 'UpdateAddress')->name('api.admin.paybetr.updateaddress');
            //Route::get('deposit', 'ListDeposits')->name('api.admin.paybetr.listdeposits');
            //Route::get('deposit/{uuid}', 'GetDeposit')->name('api.admin.paybetr.getdeposit');
            //Route::get('withdrawal', 'ListWithdrawals')->name('api.admin.paybetr.listwithdrawals');
            Route::get('withdrawal/{uuid}', 'GetWithdrawal')->name('api.admin.paybetr.getwithdrawal');
            Route::get('withdrawal/{uuid}/cancel', 'CancelWithdrawal')->name('api.admin.paybetr.cancelwithdrawal');
            Route::get('withdrawal/{uuid}/markassent', 'MarkWithdrawalAsSent')->name('api.admin.paybetr.markwithdrawalassent');
            Route::get('withdrawal/{uuid}/send', 'SendWithdrawalViaPaybetr')->name('api.admin.paybetr.sendwithdrawalviapaybetr');
        });

        /*
         * ADMIN GAMECENTER ROUTES
         * ---------------------------------------------------------------------
         */
        Route::group([
            'prefix' => 'gamecenter',
            'namespace' => 'Gamebetr\Api\Controllers\Admin\GameCenter',
        ], function () {
            Route::put('game/{uuid}', 'UpdateGame')->name('api.admin.gamecenter.updategame');
        });
    });
});
