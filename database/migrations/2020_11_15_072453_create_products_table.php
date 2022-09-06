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
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->string('barcode_no', 50);
            $table->string('name', 50);
            $table->text('description')->nullable();
            $table->text('generic_name')->nullable();
            $table->string('measurement_unit', 25)->nullable();
            $table->string('photo', 255)->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->integer('reorder_label')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->bigInteger('product_category_id')->unsigned()->index();
            $table->foreign('product_category_id')->references('id')->on('product_categories');

            $table->bigInteger('product_brand_id')->unsigned()->index();
            $table->foreign('product_brand_id')->references('id')->on('product_brands');

            $table->bigInteger('product_model_id')->unsigned()->index();
            $table->foreign('product_model_id')->references('id')->on('product_models');

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
