<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_csv_histories', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->integer('row_count');
            $table->timestamp('imported_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_csv_histories');
    }
};
