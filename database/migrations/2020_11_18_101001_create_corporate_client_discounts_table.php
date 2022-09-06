<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorporateClientDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corporate_client_discounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('corporate_client_id')->unsigned()->index();
            $table->bigInteger('hospital_id')->unsigned()->index();
            $table->bigInteger('service_id')->unsigned()->nullable();
            $table->bigInteger('service_package_id')->unsigned()->nullable();

            $table->integer('discount_percentage')->nullable();
            $table->integer('discount_amount')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->enum('discount_category', ['OPDSERVICES', 'IPDSERVICES'])->default('OPDSERVICES');

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('corporate_client_id')->references('id')->on('corporate_clients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corporate_client_discounts');
    }
}
