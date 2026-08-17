<?php

use App\Enums\PropertyStatusEnum;
use App\Enums\PropertyTypeEnum;
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
        Schema::create('property_characteristics', function (Blueprint $table) {
            $table->id(); // Add id column as primary key
            $table->unsignedBigInteger('property_id')->unique();
            $table->unsignedBigInteger('price');
            $table->integer('bathrooms')->nullable();
            $table->unsignedSmallInteger('bedrooms');
            $table->unsignedInteger('square_feet');
            $table->unsignedBigInteger('price_square_feet');
            $table->enum('property_type', [
                PropertyTypeEnum::SELF_CONTAINED->value,
                PropertyTypeEnum::ONE_BEDROOM_FLAT->value,
                PropertyTypeEnum::TWO_BEDROOM_FLAT->value,
                PropertyTypeEnum::THREE_BEDROOM_FLAT->value,
                PropertyTypeEnum::BUNGALOW->value,
                PropertyTypeEnum::DUPLEX->value,
            ]);
            $table->enum('status', [
                PropertyStatusEnum::HOLD->value,
                PropertyStatusEnum::SALE->value,
                PropertyStatusEnum::SOLD->value,
            ]);
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_characteristics');
    }
};
