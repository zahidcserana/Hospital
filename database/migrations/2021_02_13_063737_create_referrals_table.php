<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('person_id')->unsigned()->index();
            $table->enum('bill_type', ['OPD', 'IPD'])->default('OPD');
            $table->bigInteger('bill_id')->unsigned()->index();
            $table->integer('total_bill_amount');
            $table->integer('referral_amount');
            $table->enum('status', ['FULL PAID', 'PARTIAL', 'PENDING'])->default('PENDING');
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
        Schema::dropIfExists('referrals');
    }
}
