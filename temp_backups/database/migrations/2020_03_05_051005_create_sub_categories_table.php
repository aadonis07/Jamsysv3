<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('category_id')->unsigned()->index();
            $table->string('name',50)->index();
            $table->boolean('archive')->default(false);
            $table->integer('created_by')->unsigned()->index();
            $table->integer('updated_by')->unsigned()->index();
            $table->timestamps();
            //foreign
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
            $table->foreign('category_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
            $table->foreign('created_by')
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
        Schema::dropIfExists('sub_categories');
    }
}
