<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(app(config('login-via-pin.model'))->getTable(), static function (Blueprint $table): void {
            $table->string(config('login-via-pin.columns.pin'))->nullable();
            $table->timestamp(config('login-via-pin.columns.pin_valid_until'))->nullable();
        });
    }

    public function down(): void
    {
        Schema::table(app(config('login-via-pin.model'))->getTable(), static function (Blueprint $table): void {
            $table->dropColumns([
                config('login-via-pin.columns.pin'),
                config('login-via-pin.columns.pin_valid_until'),
            ]);
        });
    }
};
