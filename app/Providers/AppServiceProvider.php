<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Orchid\Platform\Dashboard;
use Orchid\Platform\ItemPermission;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Dashboard $dashboard): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        Vite::prefetch(concurrency: 3);

        $dashboard->registerPermissions(
            ItemPermission::group('Управление Блогом')
                ->addPermission('blog.posts.create', 'Создание статей')
                ->addPermission('blog.posts.edit', 'Редактирование статей')
                ->addPermission('blog.posts.delete', 'Удаление статей')
        );
    }
}
