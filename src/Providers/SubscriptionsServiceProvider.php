<?php

declare(strict_types=1);

namespace Rinvex\Subscriptions\Providers;

use Rinvex\Subscriptions\Models\Plan;
use Illuminate\Support\ServiceProvider;
use Rinvex\Support\Traits\ConsoleTools;
use Rinvex\Subscriptions\Models\PlanFeature;
use Rinvex\Subscriptions\Models\PlanSubscription;
use Rinvex\Subscriptions\Models\PlanSubscriptionUsage;
use Rinvex\Subscriptions\Console\Commands\MigrateCommand;

class SubscriptionsServiceProvider extends ServiceProvider
{
    use ConsoleTools;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected array $commands = [
        MigrateCommand::class => 'command.rinvex.subscriptions.migrate',
    ];

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'rinvex.subscriptions');

        // Bind eloquent models to IoC container
        $this->registerModels([
            'rinvex.subscriptions.plan' => Plan::class,
            'rinvex.subscriptions.plan_feature' => PlanFeature::class,
            'rinvex.subscriptions.plan_subscription' => PlanSubscription::class,
            'rinvex.subscriptions.plan_subscription_usage' => PlanSubscriptionUsage::class,
        ]);

        // Register console commands
        $this->commands($this->commands);
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
            $this->publishes([
                __DIR__.'/../../database/migrations' => database_path('migrations'),
            ], 'migrations');
        }
    }
}
