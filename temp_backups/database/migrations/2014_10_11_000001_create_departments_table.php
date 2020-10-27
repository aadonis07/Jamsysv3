<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departments', function (Blueprint $table){
            $table->increments('id');
            $table->string('name',50)->nullable();
            $table->string('code',50)->nullable();
            $table->integer('created_by')->unsigned()->index();
            $table->integer('updated_by')->unsigned()->index();
            $table->timestamps();
            //foreign
            // hindi nag wowork kapag nauna sa migration ang foreign tables
            // $table->foreign('updated_by')
            //     ->references('id')
            //     ->on('users')
            //     ->onDelete('restrict');
            // $table->foreign('created_by')
            //     ->references('id')
            //     ->on('users')
            //     ->onDelete('restrict');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departments');
    }
}
