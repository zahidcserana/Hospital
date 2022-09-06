<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIpdServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ipd_services', function (Blueprint $table) {
            $table->id();
            $table->string('code', 25);
            $table->string('name', 50);
            $table->string('description')->nullable();
            $table->decimal('tariff', 12)->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');

            $table->timestamps();

            $table->softDeletes('deleted_at', 0);


            $table->bigInteger('hospital_id')->unsigned()->index()->nullable();
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ipd_services');
    }
}
