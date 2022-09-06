<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('company_id')->unsigned()->index();
            $table->bigInteger('medicine_type_id')->unsigned()->index();
            $table->string('brand_name', 255);
            $table->string('generic_name', 255)->nullable();
            $table->string('strength', 255)->nullable();
            $table->float('tp_per_box', 15, 2)->default(0.0);
            $table->float('vat_per_box', 15, 2)->default(0.0);
            $table->float('mrp_per_box', 15, 2)->default(0.0);
            $table->integer('pcs_per_box')->nullable();
            $table->integer('pcs_per_strip')->nullable();
            $table->string('price_per_box', 50)->default(0);
            $table->integer('price_per_strip')->default(0);
            $table->integer('price_per_pcs')->default(0);
            $table->string('dar_no', 255)->nullable();
            $table->boolean('is_imported')->default(0);
            $table->boolean('is_antibiotic')->default(0);
            $table->integer('product_type')->default(0);

            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('company_id')->references('id')->on('medicine_companies')->onDelete('cascade');
            $table->foreign('medicine_type_id')->references('id')->on('medicine_types')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicines');
    }
}
