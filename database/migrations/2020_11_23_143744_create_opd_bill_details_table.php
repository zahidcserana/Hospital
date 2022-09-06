<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpdBillDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opd_bill_details', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('opd_billing_id')->unsigned()->index();
            $table->enum('item_type', ['SERVICE', 'LABTEST', 'MEDICINE', 'OTHERS', 'PACKAGE']);
            $table->bigInteger('item_id')->unsigned()->index();
            $table->string('item_name', 100);
            $table->integer('item_qty');
            $table->double('unit_tarrif');
            $table->double('total_tarrif');
            $table->double('discount')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'INITIATE']);

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('opd_billing_id')->references('id')->on('opd_billings')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opd_bill_details');
    }
}
