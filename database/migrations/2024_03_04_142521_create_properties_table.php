<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\ListingTypeEnum;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('broker_id');
            $table->string('address');
            $table->enum('listing_type', [
                ListingTypeEnum::OPEN_LISTING->value,
                ListingTypeEnum::SELL_LISTING->value,
                ListingTypeEnum::EXCLUSIVE_AGENCY_LISTING->value,
                ListingTypeEnum::NET_LISTING->value,
            ])->default(ListingTypeEnum::OPEN_LISTING->value);
            $table->string('city');
            $table->string('zip_code');
            $table->longText('description');
            $table->string('build_year');
            $table->timestamps();

            $table->foreign('broker_id')->references('id')->on('brokers')->cascadeOnDelete();
            $table->unique(['address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
