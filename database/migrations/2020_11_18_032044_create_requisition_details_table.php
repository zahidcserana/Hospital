<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisition_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('requisition_id')->unsigned()->index()->nullable();
            $table->bigInteger('product_id')->unsigned()->index()->nullable();
            $table->bigInteger('supplier_id')->unsigned()->index()->nullable();
            $table->integer('expected_quantity')->nullable();
            $table->integer('approved_quantity')->nullable();
            $table->integer('delivered_quantity')->nullable();
            $table->integer('received_quantity')->nullable();
            $table->integer('return_quantity')->nullable();
            $table->integer('damaged_quantity')->nullable();
            $table->integer('reorder_label')->nullable();
            $table->decimal('tp', 15, 2)->nullable();
            $table->decimal('mrp', 5, 2)->nullable();
            $table->string('batch_no')->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['UNAVAILABLE', 'AVAILABLE'])->default('AVAILABLE');

            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('requisition_id')->references('id')->on('requisitions')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisition_details');
    }
}
