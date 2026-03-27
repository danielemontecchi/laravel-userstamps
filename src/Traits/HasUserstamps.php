<?php

namespace DanieleMontecchi\LaravelUserstamps\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

trait HasUserstamps
{
    protected static bool $userstampsEnabled = true;

    public static function bootHasUserstamps(): void
    {
        static::creating(function (Model $model) {
            if (self::$userstampsEnabled && Auth::check() && static::hasUserstampColumn($model, 'created_by')) {
                $model->created_by ??= Auth::id();
            }

            if (self::$userstampsEnabled && Auth::check() && static::hasUserstampColumn($model, 'updated_by')) {
                $model->updated_by ??= Auth::id();
            }
        });

        static::updating(function (Model $model) {
            if (self::$userstampsEnabled && Auth::check() && static::hasUserstampColumn($model, 'updated_by')) {
                $model->updated_by = Auth::id();
            }
        });

        static::deleted(function (Model $model) {
            if (
                self::$userstampsEnabled &&
                Auth::check() &&
                static::hasUserstampColumn($model, 'deleted_by') &&
                method_exists($model, 'isForceDeleting') &&
                ! $model->isForceDeleting()
            ) {
                $model->forceFill(['deleted_by' => Auth::id()])->saveQuietly();
            }
        });

        static::restoring(function (Model $model) {
            if (self::$userstampsEnabled && static::hasUserstampColumn($model, 'deleted_by')) {
                $model->deleted_by = null;
            }
        });
    }

    protected static function hasUserstampColumn(Model $model, string $column): bool
    {
        return Schema::hasColumn($model->getTable(), $column);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            Config::get('auth.providers.users.model', \App\Models\User::class),
            'created_by'
        );
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(
            Config::get('auth.providers.users.model', \App\Models\User::class),
            'updated_by'
        );
    }

    public function destroyer(): BelongsTo
    {
        return $this->belongsTo(
            Config::get('auth.providers.users.model', \App\Models\User::class),
            'deleted_by'
        );
    }

    public static function disableUserstamps(): void
    {
        self::$userstampsEnabled = false;
    }

    public static function enableUserstamps(): void
    {
        self::$userstampsEnabled = true;
    }

    public static function isUserstampsEnabled(): bool
    {
        return self::$userstampsEnabled;
    }
}
