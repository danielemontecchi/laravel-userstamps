<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Tests\Models\Post;
use Tests\Models\User;

test('userstamps migration macros register the expected columns', function () {
    expect(Schema::hasColumns('posts', ['created_by', 'updated_by']))->toBeTrue();
});

test('softDeletesBy migration macro registers deleted_by', function () {
    expect(Schema::hasColumns('posts', ['deleted_by']))->toBeTrue();
});

test('creating a model stamps created_by and updated_by for the authenticated user', function () {
    $user = User::create(['name' => 'Alice']);

    Auth::login($user);

    $post = Post::create(['title' => 'Hello']);

    expect($post->created_by)->toBe($user->id);
    expect($post->updated_by)->toBe($user->id);
    expect($post->creator->id)->toBe($user->id);
    expect($post->updater->id)->toBe($user->id);
});

test('updating a model updates updated_by for the new authenticated user', function () {
    $author = User::create(['name' => 'Author']);
    $editor = User::create(['name' => 'Editor']);

    Auth::login($author);
    $post = Post::create(['title' => 'Draft']);

    Auth::login($editor);
    $post->title = 'Published';
    $post->save();

    expect($post->updated_by)->toBe($editor->id);
    expect($post->updater->id)->toBe($editor->id);
});

test('soft deleting a model stamps deleted_by and restoring clears it', function () {
    $user = User::create(['name' => 'Deleter']);

    Auth::login($user);
    $post = Post::create(['title' => 'Trash']);

    $post->delete();

    $trashed = Post::withTrashed()->find($post->id);
    expect($trashed->deleted_by)->toBe($user->id);

    $trashed->restore();
    expect($trashed->deleted_by)->toBeNull();
});

test('disabling userstamps prevents automatic stamping', function () {
    $user = User::create(['name' => 'NoStamp']);

    Auth::login($user);
    Post::disableUserstamps();

    $post = Post::create(['title' => 'No stamp']);

    expect($post->created_by)->toBeNull();
    expect($post->updated_by)->toBeNull();

    Post::enableUserstamps();
});
