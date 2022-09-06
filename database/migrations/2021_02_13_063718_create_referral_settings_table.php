<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReferralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('person_id')->unsigned()->index();
            $table->enum('item_type', ['SERVICE', 'PACKAGE'])->default('SERVICE');
            $table->bigInteger('service_id')->nullable();
            $table->integer('percentage_value');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->enum('service_type', ['OPD', 'OPDALL', 'IPD', 'IPDALL'])->default('OPD');
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
        Schema::dropIfExists('referral_settings');
    }
}
