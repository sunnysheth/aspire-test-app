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
        Schema::create('user_loan_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->float('amount');
            $table->integer('term');
            $table->float('amount_left');
            $table->tinyInteger('loan_status')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_loan_applications');
    }
};
