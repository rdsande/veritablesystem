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
        Schema::create('cheque_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->date('transfer_date')->nullable();
            $table->string('cheque_no')->nullable();

            $table->unsignedBigInteger('payment_transaction_id');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade');

            $table->unsignedBigInteger('payment_type_id');
            $table->foreign('payment_type_id')->references('id')->on('payment_types');

            $table->unsignedBigInteger('transfer_to_payment_type_id')->nullable();
            $table->foreign('transfer_to_payment_type_id')->references('id')->on('payment_types');

            //Each Qty Price: with or without tax
            $table->decimal('amount', 20, 4)->default(0);
            
            $table->text('note')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users'); 
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheque_transactions');
    }
};
