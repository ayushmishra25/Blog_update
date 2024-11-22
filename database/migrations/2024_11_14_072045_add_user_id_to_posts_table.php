<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->nullable();  // Add nullable foreign key
    
            // Adding the foreign key constraint
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['id']);  // Drop the foreign key
            $table->dropColumn('id');  // Drop the user_id column
        });
    }
    
};
