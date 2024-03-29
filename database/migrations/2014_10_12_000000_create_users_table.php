<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('email')->unique();

            $table->string('phone')->unique()->nullable();
            $table->string('qr_code')->unique();

            $table->string('country_code')->nullable();
            $table->string('password')->nullable();
            $table->string('image')->nullable();

            $table->string('name');
            $table->boolean('verified')->default(0);
            $table->boolean('deactivated')->default(0);

            $table->string('otp')->nullable();
            $table->string('user_verify_otp')->nullable();
            $table->string('mobile_verify_otp')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
