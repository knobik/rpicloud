<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class JobStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_status', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('node_id')->index();
            $table->boolean('failed')->default(false);
            $table->string('name');
            $table->string('description');
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('node_id')
                ->references('id')
                ->on('nodes')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('job_status');
    }
}
