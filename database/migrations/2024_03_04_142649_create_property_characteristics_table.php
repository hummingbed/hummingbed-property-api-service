<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PropertyTypeEnum;
use App\Enums\PropertyStatusEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('property_characteristics', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->unique();
            $table->integer('price')->required();
            $table->integer('bathrooms')->nullable();
            $table->integer('bedrooms')->required();
            $table->float('square_feet')->required();
            $table->float('price_square_feet')->required();
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
            ])->required();
            $table->timestamps();

            $table->foreign('property_id')->references('id')->on('properties')->cascadeOnDelete;
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
