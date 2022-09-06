<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpdBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opd_billings', function (Blueprint $table) {
            $table->id();

            $table->string('bill_no', 50);
            $table->bigInteger('patient_id')->unsigned()->index();
            $table->bigInteger('doctor_id')->unsigned()->index();
            $table->double('total_bill_amount');
            $table->double('total_discount_amount');
            $table->double('total_paid_amount');
            $table->double('total_due_amount');
            $table->enum('payment_status', ['DUE', 'PAID', 'PARTIAL']);
            $table->enum('payment_method', ['CASH', 'CARD', 'BANKTRANSFER','CHEQUE']);
            $table->text('remarks')->nullable();
            $table->enum('status', ['COMPLETE', 'INCOMPLETE', 'INITIATE']);
            $table->enum('discount_type', ['Fixed', 'Percentage'])->nullable();
            $table->integer('discount_value')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('doctor_id')->references('id')->on('doctors')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opd_billings');
    }
}
