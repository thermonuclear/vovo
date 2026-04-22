<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id()->comment('Уникальный идентификатор');
            $table->string('name')->unique()->comment('Название категории');
            $table->string('slug')->unique()->comment('URL-идентификатор для SEO и читаемых ссылок');
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id()->comment('Уникальный идентификатор');
            $table->string('name')->index()->comment('Название товара');
            $table->decimal('price', 10, 2)->comment('Цена товара');
            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete()
                ->comment('Привязка к категории');
            $table->boolean('in_stock')->default(true)->index()->comment('Наличие на складе');
            $table->float('rating')->default(0)->index()->comment('Рейтинг товара (0–5)');
            $table->softDeletes()->comment('Дата мягкого удаления');
            $table->timestamps();

            $table->index(['category_id', 'in_stock']);
            $table->fullText('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
    }
};
