<?php

require_once "../vendor/autoload.php";
require_once "../framework/database.php";

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

Capsule::schema()->dropIfExists('users');
Capsule::schema()->create('users', function (Blueprint $table) {
    $table->increments('id');
    $table->string('display_name')->nullable();
    $table->string('nickname');
    $table->string('email');
    $table->string('password');
    $table->text('description')->nullable();
    $table->integer('cover_image');
    $table->integer('profile_image');
    $table->timestamps();
});

Capsule::schema()->dropIfExists('posts');
Capsule::schema()->create('posts', function (Blueprint $table) {
    $table->increments('id');
    $table->text('text');
    $table->integer('author_id');
    $table->timestamps();
});

Capsule::schema()->dropIfExists('follows');
Capsule::schema()->create('follows', function (Blueprint $table) {
    $table->integer('follower_id');
    $table->integer('followed_id');
    $table->timestamps();
});

Capsule::schema()->dropIfExists('images');
Capsule::schema()->create('images', function (Blueprint $table) {
    $table->increments('id');
    $table->string('filename');
    $table->timestamps();
});

//Droping Schema
#Capsule::schema()->dropIfExists('users');
#Capsule::schema()->dropIfExists('posts');
#Capsule::schema()->dropIfExists('follows');
#Capsule::schema()->dropIfExists('images');
