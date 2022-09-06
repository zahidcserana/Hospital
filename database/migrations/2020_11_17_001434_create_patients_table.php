<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePatientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hospital_id')->unsigned()->index()->nullable();
            $table->bigInteger('doctor_id')->unsigned()->index()->nullable();
            $table->string('uhid', 25);
            $table->string('code', 25)->nullable();
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->enum('gender', ['MALE', 'FEMALE', 'OTHER'])->default('MALE');
            $table->integer('age')->nullable();
            $table->date('dob')->nullable();
            $table->enum('blood_group', ['Unknown', 'A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])->default('Unknown');
            $table->string('nid')->nullable();
            $table->enum('occupation', ['Business', 'Service', 'Doctor', 'Engineer', 'Agriculture', 'Housewife', 'Student', 'Teacher', 'Others'])->nullable();
            $table->string('photo')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('contact_person_mobile');
            $table->string('contact_person_relation')->nullable();
            $table->string('contact_person_address')->nullable();
            $table->integer('corporate_client_id')->nullable();
            $table->integer('employee_id')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');

            $table->timestamps();
            $table->softDeletes('deleted_at', 0);

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
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
        Schema::dropIfExists('patients');
    }
}
