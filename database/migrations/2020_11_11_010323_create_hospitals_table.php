<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHospitalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('code', 25);
            $table->string('name', 50);
            $table->string('district', 25)->nullable();
            $table->text('address')->nullable();
            $table->string('contact', 25)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('website_url', 100)->nullable();
            $table->string('logo_image', 255)->nullable();
            $table->string('mobile_no', 25)->nullable();
            $table->string('tnt_no', 25)->nullable();
            $table->string('contact_person_name', 50)->nullable();
            $table->string('contact_person_mobile', 25)->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
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
        Schema::dropIfExists('hospitals');
    }
}
