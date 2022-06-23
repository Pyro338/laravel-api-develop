<?php

namespace Gamebetr\Api\Providers;

use Gamebetr\Api\Commands\CreateAccountsForUser;
use Gamebetr\Api\Commands\CreateBank;
use Gamebetr\Api\Commands\CreatePrize;
use Gamebetr\Api\Commands\Install;
use Gamebetr\Api\Commands\OverrideAffiliate;
use Gamebetr\Api\Commands\ShowAffiliate;
use Gamebetr\Api\Commands\ShowBanks;
use Gamebetr\Api\Commands\ShowBetback;
use Gamebetr\Api\Commands\ShowLossback;
use Gamebetr\Api\Commands\ShowVip;
use Gamebetr\Api\Commands\SyncAccounts;
use Gamebetr\Api\Events\GameCenterTransactionEvent;
use Gamebetr\Api\Events\UserLoggedIn;
use Gamebetr\Api\Listeners\GameCenterTransactionListener;
use Gamebetr\Api\Listeners\UserLoggedInListener;
use Gamebetr\Api\Services\AffiliateProcessService;
use Gamebetr\Api\Services\AffiliateService;
use Gamebetr\Api\Services\ApiRequestService;
use Gamebetr\Api\Services\ApiService;
use Gamebetr\Api\Services\AvatarService;
use Gamebetr\Api\Services\Bank\Tags;
use Gamebetr\Api\Services\BankService;
use Gamebetr\Api\Services\GameCenterService;
use Gamebetr\Api\Services\LeaderboardService;
use Gamebetr\Api\Services\PaybetrService;
use Gamebetr\Api\Services\PlaybetrService;
use Gamebetr\Api\Services\PrizeService;
use Gamebetr\Api\Services\Prize\FreeSpinsService;
use Gamebetr\Api\Services\Prize\StatusPointsService;
use Gamebetr\Api\Services\SupportService;
use Gamebetr\Api\Services\TemplateService;
use Gamebetr\Api\Services\TransactionBatchService;
use Gamebetr\Api\Services\UriService;
use Gamebetr\Api\Services\UserService;
use Gamebetr\Api\Services\UserSingleton;
use Gamebetr\Api\Services\VipService;
use Gamebetr\Api\Services\VipProcessService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register method.
     * @return void
     */
    public function register()
    {
        // Affiliate facade
        $this->app->bind('affiliate-service', function () {
            return new AffiliateService();
        });
        // Affiliate Process facade
        $this->app->bind('affiliate-process-service', function () {
            return new AffiliateProcessService();
        });
        // Api facade
        $this->app->bind('api-service', function () {
            return new ApiService();
        });
        // ApiRequest facade
        $this->app->bind('api-request-service', function () {
            return new ApiRequestService();
        });
        // Avatar facade
        $this->app->bind('avatar-service', function () {
            return new AvatarService();
        });
        // Bank facade
        $this->app->bind('bank-service', function () {
            return new BankService();
        });
        // Prize facade
        $this->app->bind('prize-service', function () {
            return new PrizeService();
        });
        // FreeSpins facade
        $this->app->bind('free-spins-service', function () {
            return new FreeSpinsService();
        });
        // StatusPoints facade
        $this->app->bind('status-points-service', function () {
            return new StatusPointsService();
        });
        $this->app->bind('bank-tags', static function () {
            return new Tags();
        });
        // Game Center facade
        $this->app->bind('game-center-service', function () {
            return new GameCenterService();
        });
        // Paybetr facade
        $this->app->bind('paybetr-service', function () {
            return new PaybetrService();
        });
        // Playbetr facade
        $this->app->bind('playbetr-service', function () {
            return new PlaybetrService();
        });
        // Template facade
        $this->app->bind('template-service', function () {
            return new TemplateService();
        });
        // Transaction Batch facade
        $this->app->bind('transaction-batch-service', function () {
            return new TransactionBatchService();
        });
        // URI service
        $this->app->bind('uri-service', function () {
            return new UriService();
        });
        // User facade
        $this->app->bind('user-service', function () {
            return new UserService();
        });
        // User singleton
        $this->app->singleton(UserSingleton::class, function () {
            return new UserSingleton();
        });
        // User singleton facade
        $this->app->bind('user-singleton', function () {
            return $this->app->make(UserSingleton::class);
        });
        // Vip facade
        $this->app->bind('vip-service', function () {
            return new VipService();
        });
        // Vip Process facade
        $this->app->bind('vip-process-service', function () {
            return new VipProcessService();
        });
        // Support Process facade
        $this->app->bind('support-service', function () {
            return new SupportService();
        });
        $this->app->bind('leaderboard', static function () {
            return new LeaderboardService();
        });
        // Commands
        $this->commands([
            CreateAccountsForUser::class,
            CreateBank::class,
            CreatePrize::class,
            Install::class,
            OverrideAffiliate::class,
            ShowAffiliate::class,
            ShowBanks::class,
            ShowBetback::class,
            ShowLossback::class,
            ShowVip::class,
            SyncAccounts::class,
        ]);
        // Scheduled Tasks
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            //$schedule->command('api:sync-paybetr-keys')->hourly();
        });
        // force https
        URL::forceScheme('https');
    }

    /**
     * Boot method.
     * @return void
     */
    public function boot()
    {
        // config
        $this->mergeConfigFrom(__DIR__.'/../../config/api.php', 'api');
        $this->mergeConfigFrom(__DIR__.'/../../config/paybetr.php', 'paybetr');
        $this->mergeConfigFrom(__DIR__.'/../../config/vip.php', 'vip');
        $this->mergeConfigFrom(__DIR__ . '/../../config/content-api.php', 'content-api.settings');
        //$this->publishes([__DIR__.'/../../config' => config_path()], 'config');
        // migrations
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        //$this->publishes([__DIR__.'/../../database' => database_path()], 'migrations');
        // routes
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        // views
        $this->loadViewsFrom(__DIR__.'/../../views', 'api');
        $this->publishes([__DIR__.'/../../views' => resource_path('views/vendor/api')], 'api');
        // assets
        $this->publishes([__DIR__.'/../../assets' => public_path('gamebetr')], 'public');
        // event listeners
        Event::listen(
            GameCenterTransactionEvent::class,
            [GameCenterTransactionListener::class, 'handle']
        );
        Event::listen(
            UserLoggedIn::class,
            [UserLoggedInListener::class, 'handle']
        );
        //Event::listen(
            //UserUpdatedEvent::class,
            //[UserUpdatedListener::class, 'handle']
        //);
    }
}
