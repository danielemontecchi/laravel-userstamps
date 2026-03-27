<?php

use DanieleMontecchi\LaravelUserstamps\Traits\InteractsWithUserstamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tests\Models\Post;

class InteractsWithUserstampsModel extends Model
{
    use InteractsWithUserstamps;

    protected $table = 'users';
    protected $guarded = [];
}

test('interacts with userstamps exposes the expected related models', function () {
    $model = new InteractsWithUserstampsModel();

    $createdModels = $model->createdModels(Post::class);
    expect($createdModels)->toBeInstanceOf(HasMany::class);
    expect($createdModels->getForeignKeyName())->toBe('created_by');
    expect($createdModels->getRelated()->getTable())->toBe('posts');

    $updatedModels = $model->updatedModels(Post::class);
    expect($updatedModels)->toBeInstanceOf(HasMany::class);
    expect($updatedModels->getForeignKeyName())->toBe('updated_by');

    $deletedModels = $model->deletedModels(Post::class);
    expect($deletedModels)->toBeInstanceOf(HasMany::class);
    expect($deletedModels->getForeignKeyName())->toBe('deleted_by');
});
