<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** Note / Rules
            visible by department_id ( per user )
            payment_types
                IF DATED-CHECKS, Null payment terms
                IF COD, Null payment terms
                IF WITH TERMS, Not Null payment terms

            **Conditions
                unique value with where 'department_id,archive = 0 ( false )'
         *
        **/
        Schema::create('suppliers', function (Blueprint $table){
            $table->increments('id');
            $table->string('name',150)->index();
            $table->string('code',20)->index();
            $table->integer('department_id')->unsigned()->index();
            $table->integer('industry_id')->unsigned()->index();
            $table->boolean('archive')->default(false);
            $table->enum('category',['GOODS','SERVICES'])->index()->nullable();
            $table->string('tin_number',20)->nullable();
            $table->boolean('vatable')->default(false)->index(); // false = non vat true = vatable
            $table->string('contact_person',150);
            $table->string('contact_number',150); // multiple contact number
            $table->string('email',50)->nullable();
            $table->string('complete_address',100);
            $table->integer('payment_terms')->nullable();
            $table->enum('payment_type',['DATED-CHECKS','COD','WITH-TERMS']); //if with terms Selected, payment terms has a value
            $table->text('payment_note')->nullable();
            $table->text('remarks')->nullable();
            $table->integer('created_by')->unsigned()->index();
            $table->integer('updated_by')->unsigned()->index();

            //foreign
            $table->foreign('industry_id')
                ->references('id')
                ->on('industries')
                ->onDelete('restrict');
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('restrict');
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
        Schema::dropIfExists('suppliers');
    }
}
