<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDoctorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10);
            $table->enum('type', ['INHOUSE', 'OUTHOUSE'])->default('INHOUSE');
            $table->string('name', 50);
            $table->string('bmdc_id', 25)->nullable();
            $table->string('mobile', 25);
            $table->string('email', 50)->nullable();
            $table->string('institute_name', 255)->nullable();
            $table->text('chamber_address')->nullable();
            $table->string('photo', 255)->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

            $table->timestamps();

            $table->softDeletes('deleted_at', 0);

            $table->bigInteger('hospital_id')->unsigned()->index();
            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');

            $table->bigInteger('department_id')->unsigned()->nullable();
            $table->foreign('department_id')->references('id')->on('departments');

            $table->bigInteger('doctors_specialities_id')->unsigned();
            $table->foreign('doctors_specialities_id')->references('id')->on('doctors_specialities');

            $table->bigInteger('designation_id')->unsigned();
            $table->foreign('designation_id')->references('id')->on('designations');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('doctors');
    }
}
