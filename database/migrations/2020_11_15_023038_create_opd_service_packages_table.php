<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpdServicePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opd_service_packages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id')->unsigned()->index()->nullable();
            $table->string('code', 25);
            $table->string('name', 50);
            $table->json('services');
            $table->decimal('tariff', 12)->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');

            $table->timestamps();

            $table->softDeletes('deleted_at', 0);


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
        Schema::dropIfExists('opd_service_packages');
    }
}
