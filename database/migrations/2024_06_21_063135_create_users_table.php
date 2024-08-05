<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('fullname')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile',255)->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->string('business_city')->nullable();
            $table->string('business_state')->nullable();
            $table->string('business_zipcode')->nullable();
            $table->boolean('community_owner')->nullable();
            $table->integer('location')->default(1)->comment('1:Atlanta,2:Columbus,3:Augusta,4:Macon');
            $table->tinyInteger('mailverified')->nullable();
            $table->string('password',255)->nullable();
            $table->string('status',10)->nullable()->default(1);
            $table->string('device_token')->nullable();
            $table->integer('type')->default(2)->comment('1:Admin,2:User');
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
};
