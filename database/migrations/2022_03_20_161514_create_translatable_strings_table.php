<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('translatable_strings', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('scope')->nullable();
            $table->string('name');
            $table->string('key')->nullable();
            $table->json('value');
            $table->boolean('is_html');
        });
    }
};
