<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employees', function (Blueprint $table){
            $table->increments('id');
            // personal information
            $table->integer('employee_num')->unsigned()->index()->nullable();
            $table->string('first_name',50)->index();
            $table->string('last_name',50)->index();
            $table->string('middle_name',50)->nullable();
            $table->string('prefix',20)->nullable();
            $table->date('birth_date')->index();
            $table->enum('gender',['MALE','FEMALE'])->index();
            $table->enum('civil_status',[
                        'SINGLE',
                        'MARRIED',
                        'DIVORCED',
                        'WIDOW',
                        'WIDOWER',
                        'ANNULLED',
                        'COMMON-LAW-WIFE',
                        'COMMON-LAW-HUSBAND',
                    ])->default('SINGLE')->index();
            $table->text('address')->nullable();
            $table->string('contact_number',50)->nullable();// multiple contacts
            $table->string('email',50)->nullable();
            $table->string('sss',25)->nullable();
            $table->string('pagibig',25)->nullable();
            $table->string('philhealth',25)->nullable();
            $table->string('tin',25)->nullable();

            $table->string('access_code',10)->index()->nullable(); // for hr officer
            $table->integer('position_id')->unsigned()->index()->nullable(); // foreign
            $table->integer('department_id')->unsigned()->index()->nullable(); // foreign
            $table->string('section',50)->nullable(); // for production purposes
            $table->date('date_hired')->nullable();
            $table->date('regularization_date')->nullable();
            $table->date('date_regulized')->nullable();
            $table->enum('status',['HIRED','REGULAR','RESIGNED','PROBATIONARY','END-OF-CONTRACT','TERMINATED'])->nullable(); // for approval
            $table->decimal('basic_salary',15,2)->nullable();
            $table->decimal('allowance',15,2)->nullable();
            $table->decimal('gross_salary',15,2)->nullable();
            $table->boolean('tax_exemp')->default(false);
            $table->date('date_resigned')->nullable();
            $table->boolean('separation_pay')->default(false);
            $table->integer('created_by')->unsigned()->index();
            $table->integer('updated_by')->unsigned()->index();
            $table->timestamps();
            //foreign
            $table->foreign('position_id')
                ->references('id')
                ->on('positions')
                ->onDelete('restrict');
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('restrict');
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('employees');
    }
}
