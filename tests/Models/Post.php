<?php

namespace Tests\Models;

use DanieleMontecchi\LaravelUserstamps\Traits\HasUserstamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasUserstamps;
    use SoftDeletes;

    protected $guarded = [];
}
