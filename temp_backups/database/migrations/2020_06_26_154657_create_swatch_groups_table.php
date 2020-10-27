<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSwatchGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){
        Schema::create('swatch_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50)->index();
            $table->integer('parent_id')->unsigned()->index()->nullable();
            $table->integer('swatch')->unsigned()->index()->nullable(); //swatch_name
            $table->integer('swatch_id')->unsigned()->index()->nullable();
            $table->integer('sub_category_id')->unsigned()->index()->nullable();
            $table->integer('order')->unsigned()->index()->nullable();
            $table->boolean('archive')->default(false);
            $table->enum('category',[
                    'FABRIC',
                    'LAMINATES',
                    'WINDOW BLINDS',
                    'LEATHERRETTE',
                    'EDGEBAND',
                    'CARPET',
                    'GRANITE',
                    'PARTITION FABRIC',
                    'HARDWOOD',
                    'DAMANTEX',
                ])->index();
            $table->integer('created_by')->unsigned()->index();
            $table->integer('updated_by')->unsigned()->index();
            $table->timestamps();
            // foreigns
            $table->foreign('parent_id')
                ->references('id')
                ->on('swatch_groups')
                ->onDelete('restrict');
            $table->foreign('swatch_id')
                ->references('id')
                ->on('swatches')
                ->onDelete('restrict');
            $table->foreign('sub_category_id')
                ->references('id')
                ->on('sub_categories')
                ->onDelete('restrict');
            $table->foreign('updated_by')
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
        Schema::dropIfExists('swatch_groups');
    }
}
