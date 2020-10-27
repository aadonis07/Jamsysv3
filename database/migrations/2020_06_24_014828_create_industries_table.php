<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndustriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',150);
            $table->boolean('is_active')->default(true);
            $table->integer('created_by')->unsigned()->index();
            $table->integer('updated_by')->unsigned()->index();
             $table->foreign('updated_by')
                 ->references('id')
                 ->on('users')
                 ->onDelete('restrict');
             $table->foreign('created_by')
                 ->references('id')
                 ->on('users')
                 ->onDelete('restrict');
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
        Schema::dropIfExists('industries');
    }
}
