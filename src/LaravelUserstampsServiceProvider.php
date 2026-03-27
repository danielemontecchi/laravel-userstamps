<?php

namespace DanieleMontecchi\LaravelUserstamps;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class LaravelUserstampsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blueprint::macro('userstamps', function () {
            /** @var Blueprint $this */
            $userModel = Config::get('auth.providers.users.model', \App\Models\User::class);
            $userTable = (new $userModel())->getTable();

            $this->foreignId('created_by')->nullable()->constrained($userTable)->nullOnDelete();
            $this->foreignId('updated_by')->nullable()->constrained($userTable)->nullOnDelete();
        });

        Blueprint::macro('softDeletesBy', function ($column = 'deleted_by') {
            /** @var Blueprint $this */
            $userModel = Config::get('auth.providers.users.model', \App\Models\User::class);
            $userTable = (new $userModel())->getTable();

            $this->foreignId($column)->nullable()->constrained($userTable)->nullOnDelete();
        });
    }

    public function register(): void
    {
        //
    }
}
