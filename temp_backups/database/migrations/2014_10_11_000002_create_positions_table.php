<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
class CreatePositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('positions', function (Blueprint $table){
            $table->bigIncrements('id');
            $table->string('name',50)->nullable();
            $table->integer('department_id')->unsigned()->index();
            $table->string('deparment_code',10)->comment = 'belongs to departments';
            $table->integer('created_by')->unsigned()->index();
            $table->integer('updated_by')->unsigned()->index();
            $table->timestamps();
            //foreign
            // hindi nag wowork kapag nauna sa migration ang foreign tables
            // $table->foreign('created_by')
            // ->references('id')
            // ->on('users')
            // ->onDelete('restrict');
            // $table->foreign('updated_by')
            // ->references('id')
            // ->on('users')
            // ->onDelete('restrict');
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('positions');
    }
}
