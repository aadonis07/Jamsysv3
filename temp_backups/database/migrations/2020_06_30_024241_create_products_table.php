<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('products', function (Blueprint $table){
            $table->increments('id');
            $table->string('product_name',150)->nullable()->index();
            $table->integer('parent_id')->nullable()->unsigned()->index();
            $table->integer('sub_category_id')->nullable()->unsigned()->index();
            $table->integer('category_id')->nullable()->unsigned()->index();
            $table->integer('department_id')->unsigned()->index(); //base on user's account
            $table->boolean('is_default')->nullable()->index(); //base on user's account
            $table->string('swatches',50)->nullable();
            $table->enum('status',['R-APPROVAL','APPROVED','DECLINED'])->index()->default('R-APPROVAL');
            $table->enum('type',['SUPPLY','RAW','SPECIAL-ITEM','CUSTOMIZED','COMBINATION'])->index()->default('SUPPLY');
            $table->text('remarks')->nullable();
            $table->integer('created_by')->index()->unsigned()->nullable();
            $table->integer('updated_by')->index()->unsigned()->nullable();
            $table->boolean('archive')->default(false)->index();
            //TEXT FILE FOR DESCRIPTION
            //foreign
            $table->foreign('updated_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
            $table->foreign('parent_id')
                ->references('id')
                ->on('products')
                ->onDelete('restrict');
            $table->foreign('sub_category_id')
                ->references('id')
                ->on('sub_categories')
                ->onDelete('restrict');
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('restrict');
            $table->foreign('department_id')
                ->references('id')
                ->on('departments')
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
        Schema::dropIfExists('products');
    }
}
