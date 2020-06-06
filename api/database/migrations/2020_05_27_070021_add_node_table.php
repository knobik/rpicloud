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
            $table->string('mac')->nullable()->unique();
            $table->boolean('netboot')->default(false);
            $table->string('arch')->nullable();
            $table->unsignedInteger('cpus')->nullable();
            $table->unsignedInteger('cpu_max_freq')->nullable();
            $table->unsignedInteger('ram_max')->nullable();

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
