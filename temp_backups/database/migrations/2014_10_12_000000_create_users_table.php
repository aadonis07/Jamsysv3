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
        Schema::create('users', function (Blueprint $table){
            $table->increments('id');
            $table->string('username',150)->index()->comment = 'fullname+employee_id';
            $table->string('password');
            $table->string('email',100)->unique()->comment = 'Company Email';
            $table->timestamp('email_verified_at')->nullable();
            $table->string('nickname',20)->nullable();
            $table->integer('employee_id')->unsigned()->index();
            $table->integer('department_id')->unsigned()->index();
            $table->integer('team_id')->unsigned()->index();
            $table->string('department_code',10)->comment = 'belongs to departments';
            $table->integer('position_id')->unsigned()->index();
            $table->enum('status',['ACTIVE','INACTIVE'])->default('ACTIVE')->index();
            $table->boolean('archive')->default(false);
            $table->boolean('is_secured')->default(true)->index()->comment = 'If false, can login out of office.';
            $table->boolean('is_lock')->default(false)->index()->comment = 'Lock Page';
            $table->dateTime('last_login')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->macAddress('local')->nullable();
            $table->integer('created_by')->unsigned()->index();
            $table->integer('updated_by')->unsigned()->index();
            $table->rememberToken();

            //foreign
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('restrict');
                $table->foreign('team_id')
                ->references('id')
                ->on('teams')
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
        Schema::dropIfExists('users');
    }
}
