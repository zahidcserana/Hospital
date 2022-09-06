<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();

            $table->enum('billing_type', ['OPD', 'IPD'])->default('OPD');
            $table->integer('billing_id');
            $table->double('payment_amount');
            $table->enum('payment_method', ['CASH', 'CARD', 'MBANKING']);
            $table->date('payment_date');
            $table->text('remarks')->nullable();
            $table->enum('status', ['FULL', 'PARTIAL']);

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_histories');
    }
}
