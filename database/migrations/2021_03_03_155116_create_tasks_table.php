<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('title');
            $table->longText('content')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->integer('status')->default(0);               // Ongoing Task = 0 & Completed Task = 1
            $table->dateTime('task_date')->default(Carbon::now()->toDateString());
            /*  $table->string('task_date')->default(Carbon::now()->toDateString());
            or  $table->date('task_date')->default(Carbon::now()); */
            $table->timestamps();







            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
