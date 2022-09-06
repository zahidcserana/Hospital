<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedicineConsumptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicine_consumptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id')->unsigned()->index();
            $table->bigInteger('patient_id')->unsigned()->index();
            $table->bigInteger('medicine_id')->unsigned()->index();
            $table->integer('quantity');
            $table->integer('serial')->nullable();
            $table->string('remarks', 255)->nullable();
            $table->date('date')->nullable();

            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicine_consumptions');
    }
}
