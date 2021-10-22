<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('ip');
            $table->string('hostname')->nullable();
            $table->string('mac')->nullable();
            $table->boolean('netboot')->default(false);
            $table->boolean('netbooted')->default(false);
            $table->boolean('online')->default(false);
            $table->string('arch')->nullable();
            $table->string('model')->nullable();
            $table->string('bootloader_timestamp')->nullable();
            $table->string('boot_order')->nullable();
            $table->unsignedInteger('cpus')->nullable();
            $table->unsignedInteger('cpu_max_freq')->nullable();
            $table->unsignedInteger('ram_max')->nullable();
            $table->text('storage_devices')->default('[]');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nodes');
    }
}
