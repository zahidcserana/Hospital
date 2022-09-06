<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index()->nullable();
            $table->bigInteger('hospital_id')->unsigned()->index()->nullable();
            $table->bigInteger('department_id')->unsigned()->index()->nullable();
            $table->bigInteger('requisition_category_id')->unsigned()->index()->nullable();
            $table->bigInteger('supplier_id')->unsigned()->index()->nullable();
            $table->date('expected_date')->nullable();
            $table->integer('type')->nullable();
            $table->string('requisition_no')->nullable();
            $table->decimal('total_value', 12)->nullable();
            $table->string('attached_hard_copy')->nullable();
            $table->enum('status', ['INITIATING', 'INITIATED', 'APPROVED_BY_FD', 'ACCEPTED', 'APPROVED_BY_GM', 'APPROVED_BY_DF', 'PROCESSING', 'DELIVERED', 'RECEIVED', 'ARCHIVED', 'VERIFIED', 'APPROVED_BY_MS', 'LOCAL_PURCHASE'])->default('INITIATING');
            $table->dateTime('delivered_date')->nullable();
            $table->dateTime('receive_date')->nullable();
            $table->string('remarks')->nullable();
            $table->enum('purchase_from', ['HEAD_OFFICE', 'LOCAL'])->default('HEAD_OFFICE');
            $table->enum('purchase_type', ['WORK_ORDER', 'CASH'])->default('WORK_ORDER');

            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('requisition_category_id')->references('id')->on('product_categories')->onDelete('cascade');
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
        Schema::dropIfExists('requisitions');
    }
}
