<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(app(config('pin-login.model'))->getTable(), static function (Blueprint $table): void {
            $table->string(config('pin-login.columns.pin'))->nullable();
            $table->timestamp(config('pin-login.columns.pin_valid_until'))->nullable();
        });
    }

    public function down(): void
    {
        Schema::table(app(config('pin-login.model'))->getTable(), static function (Blueprint $table): void {
            $table->dropColumn([
                config('pin-login.columns.pin'),
                config('pin-login.columns.pin_valid_until'),
            ]);
        });
    }
};
