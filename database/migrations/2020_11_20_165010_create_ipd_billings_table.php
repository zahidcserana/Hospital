<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpdBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipd_billings', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('patient_admission_id')->unsigned()->index();
            $table->string('bill_no', 50)->nullable();
            $table->double('total_bill_amount');
            $table->double('total_discount_amount');
            $table->double('total_paid_amount');
            $table->double('total_due_amount');
            $table->double('total_advance_amount');
            $table->enum('payment_status', ['DUE', 'PAID', 'PARTIAL']);
            $table->text('remarks')->nullable();
            $table->enum('status', ['COMPLETE', 'INCOMPLETE', 'INITIATE']);
            $table->enum('discount_type', ['Fixed', 'Percentage'])->nullable();
            $table->integer('discount_value')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('patient_admission_id')->references('id')->on('patient_admissions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ipd_billings');
    }
}
