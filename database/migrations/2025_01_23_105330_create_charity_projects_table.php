<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharityProjectsTable extends Migration
{
    public function up(): void
    {
        Schema::create('charity_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 128)->unique();
            $table->string('short_description', 5000);
            $table->enum('status', ['draft', 'active', 'closed']);
            $table->timestamp('launch_date');
            $table->string('additional_description', 50000)->nullable();
            $table->unsignedInteger('donation_amount')->default(0);
            $table->integer('sort_order')->default(1000000);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charity_projects');
    }
}
