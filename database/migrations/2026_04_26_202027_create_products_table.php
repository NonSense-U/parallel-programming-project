<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('stock');
            $table->decimal('price', 10, 2);
            $table->timestamps();
        });

        DB::statement("
            ALTER TABLE products
            ADD CONSTRAINT check_stock_non_negative
            CHECK (stock >= 0)
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
        DB::statement("
        ALTER TABLE products
        DROP CONSTRAINT check_stock_non_negative
       ");
    }
};
