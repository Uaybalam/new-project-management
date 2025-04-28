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
        Schema::create('entities', function (Blueprint $table) { 
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('entity_status')->nullable();
            $table->string('website')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('business_address')->nullable();
            $table->string('document_folder_link')->nullable();
            $table->date('incorporation_date')->nullable();
            $table->string('formally_known_as')->nullable();
            $table->string('doing_business_as')->nullable();
            $table->date('effective_entity_type_date')->nullable();
            $table->string('state_of_registration')->nullable();
            $table->string('industry')->nullable();
            $table->integer('number_of_employees')->nullable();
            $table->string('revenue_range')->nullable();
            $table->foreignId('assigned_am_id')->nullable()->constrained('users');
            $table->foreignId('assigned_tm_id')->nullable()->constrained('users');
            $table->foreignId('assigned_sa_id')->nullable()->constrained('users');
            
            $table->text('notes')->nullable();
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
        Schema::dropIfExists('entities');
    }
};
