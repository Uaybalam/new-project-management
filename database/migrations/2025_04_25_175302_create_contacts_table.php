<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->text('notes')->nullable();
            $table->string('ssn')->nullable();
            $table->string('ssn_itin_copy')->nullable(); // File Upload for SSN/ITIN

            // Drivers License
            $table->string('drivers_license')->nullable();
            $table->string('drivers_license_copy')->nullable(); // File Upload for Drivers License

            // Date of Birth
            $table->date('date_of_birth')->nullable();

            // PIT Filing Status and PIT Copy
            $table->string('pit_filing_status')->nullable();
            $table->string('pit_copy')->nullable(); // File Upload for PIT Copy

            // Spouse Information
            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_last_name')->nullable();
            $table->string('spouse_ssn_itin')->nullable(); // Spouse SSN/ITIN
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
};
