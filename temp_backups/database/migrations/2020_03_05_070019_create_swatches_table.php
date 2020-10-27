<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSwatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('swatches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->enum('category',
                [
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
                ]);
            $table->enum('status',['ACTIVE','INACTIVE']);
            $table->timestamps();
            $table->integer('created_by')->unsigned()->index();
            $table->integer('updated_by')->unsigned()->index();
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
        Schema::dropIfExists('swatches');
    }
}
