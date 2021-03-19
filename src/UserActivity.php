<?php

namespace Tino\UserActivity;

use Event;
use Route;
use Tino\Plugins\Plugin;
use Tino\Support\Sidebar\Item;
use Tino\UserActivity\Http\View\Composers\ShowUserComposer;
use Tino\UserActivity\Listeners\PermissionEventsSubscriber;
use Tino\UserActivity\Listeners\RoleEventsSubscriber;
use Tino\UserActivity\Listeners\UserEventsSubscriber;
use Tino\UserActivity\Repositories\Activity\ActivityRepository;
use Tino\UserActivity\Repositories\Activity\EloquentActivity;
use View;

class UserActivity extends Plugin
{
    /**
     * {@inheritDoc}
     */
    public function sidebar()
    {
        return Item::create(__('Activity Log'))
            ->route('activity.index')
            ->icon('fas fa-server')
            ->active("activity*")
            ->permissions('users.activity');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ActivityRepository::class, EloquentActivity::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'user-activity');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'user-activity');
        $this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations')
        ], 'migrations');

        $this->app->booted(function () {
            $this->mapWebRoutes();

            if ($this->app['config']->get('auth.expose_api')) {
                $this->mapApiRoutes();
            }
        });

        $this->attachViewComposers();

        $this->registerEventListeners();
    }

    /**
     * Map web plugin related routes.
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'namespace' => 'Tino\UserActivity\Http\Controllers\Web',
            'middleware' => 'web',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * Map API plugin related routes.
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'namespace' => 'Tino\UserActivity\Http\Controllers\Api',
            'middleware' => 'api',
            'prefix' => 'api',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        });
    }

    /**
     * Register event subscribers for the plugin.
     */
    private function registerEventListeners()
    {
        Event::subscribe(PermissionEventsSubscriber::class);
        Event::subscribe(RoleEventsSubscriber::class);
        Event::subscribe(UserEventsSubscriber::class);
    }

    /**
     * Attach view composers to add necessary data to the view.
     */
    private function attachViewComposers()
    {
        View::composer('user.view', ShowUserComposer::class);
    }
}
