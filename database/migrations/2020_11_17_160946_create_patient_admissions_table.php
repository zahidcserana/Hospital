<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientAdmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_admissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('patient_id')->unsigned()->index()->nullable();
            $table->bigInteger('hospital_id')->unsigned()->index()->nullable();
            $table->bigInteger('doctor_id')->unsigned()->index()->nullable();
            $table->string('bill_no', 50);
            $table->string('floor')->nullable();
            $table->string('room')->nullable();
            $table->string('description')->nullable();
            $table->date('admission_date')->nullable();
            $table->date('release_date')->nullable();
            $table->enum('status', ['Admission', 'Release'])->default('Admission');

            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
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
        Schema::dropIfExists('patient_admissions');
    }
}
