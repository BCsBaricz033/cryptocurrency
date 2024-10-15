<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->string('api_id', 40);
            $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->float('amount');
            $table->float('price');
            $table->float('total');
            $table->float('price_change_24h')->nullable();
            $table->float('price_change_percentage_24h')->nullable();
            $table->string('image',100)->nullable();
            $table->timestamps();
        });
    }
        

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            
        });
        Schema::dropIfExists('wallets');
    }
};
