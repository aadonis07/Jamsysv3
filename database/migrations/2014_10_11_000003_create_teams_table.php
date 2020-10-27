<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table){
            $table->increments('id');
            $table->string('name',50)->index();
            $table->string('display_name',100)->index();
            $table->enum('branch',['MAKATI','QUEZON-CITY'])->default('QUEZON-CITY')->index()->comment = 'helpers nalang ang address';
            $table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE')->index();
            $table->string('telephone',255)->index();
            $table->string('team_manager',255)->index();
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
        Schema::dropIfExists('teams');
    }
}
