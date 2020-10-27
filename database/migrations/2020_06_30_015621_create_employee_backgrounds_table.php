<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeBackgroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_backgrounds', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_id')->unsigned()->index()->nullable();
            $table->enum('type',['EDUCATION','WORK','FAMILY'])->default('FAMILY');
            $table->string('name',100); // EDUCATION - SCHOOL NAME, FAMILY - FAMILY NAME, WORK - COMPANY NAME
            $table->string('position',100); // EDUCATION - DEGREE   , FAMILY - OCCUPATION, WORK - POSITION IN WORK
            $table->string('relationship',100)->nullable(); // EDUCATION - COURSE   , FAMILY - Relation, WORK - NULL
            $table->string('contact_number',50)->nullable();
            $table->timestamps();
            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
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
        Schema::dropIfExists('employee_backgrounds');
    }
}
