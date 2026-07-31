<?php

namespace Tests\Feature;

use App\Models\ProductVariant;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductVariantApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('fragrance', function (Blueprint $table): void {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('variant_type', function (Blueprint $table): void {
            $table->id();
            $table->string('code');
            $table->string('name');
        });

        Schema::create('product_variant', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('fragrance_id');
            $table->foreignId('variant_type_id');
            $table->decimal('bottle_size_ml', 10, 2)->nullable();
            $table->decimal('base_price', 15, 2);
            $table->decimal('cost_ml', 15, 2);
            $table->string('mix_ratio', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function test_it_returns_paginated_variants_with_relations(): void
    {
        $fragranceId = $this->createFragrance();
        $variantTypeId = $this->createVariantType();
        $variant = ProductVariant::create($this->variantData($fragranceId, $variantTypeId));

        $this->getJson('/api/product-variants?fragrance_name=Vanilla&variant_type_code=BTL&bottle_size_ml=30')
            ->assertOk()
            ->assertJsonPath('data.0.id', $variant->id)
            ->assertJsonPath('data.0.fragrance.name', 'Vanilla')
            ->assertJsonPath('data.0.variant_type.name', 'Bottle')
            ->assertJsonStructure(['data', 'current_page', 'last_page', 'per_page', 'total']);

        ProductVariant::create($this->variantData($fragranceId, $variantTypeId));

        $this->getJson('/api/product-variants?limit=1&offset=0')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.limit', 1)
            ->assertJsonPath('meta.offset', 0)
            ->assertJsonPath('meta.next_offset', 1)
            ->assertJsonPath('meta.has_more', true);
    }

    public function test_it_creates_shows_updates_and_deletes_a_variant(): void
    {
        $fragranceId = $this->createFragrance();
        $variantTypeId = $this->createVariantType();

        $created = $this->postJson('/api/product-variants', $this->variantData($fragranceId, $variantTypeId))
            ->assertCreated()
            ->assertJsonPath('data.fragrance.name', 'Vanilla')
            ->json('data');

        $this->getJson('/api/product-variants/'.$created['id'])
            ->assertOk()
            ->assertJsonPath('data.id', $created['id']);

        $this->patchJson('/api/product-variants/'.$created['id'], ['base_price' => 30000])
            ->assertOk()
            ->assertJsonPath('data.base_price', 30000);

        $this->deleteJson('/api/product-variants/'.$created['id'])->assertNoContent();
        $this->assertDatabaseMissing('product_variant', ['id' => $created['id']]);
    }

    public function test_it_validates_mobile_api_input(): void
    {
        $this->postJson('/api/product-variants', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['fragrance_id', 'variant_type_id', 'base_price', 'cost_ml']);
    }

    private function createFragrance(): int
    {
        return Schema::getConnection()->table('fragrance')->insertGetId([
            'code' => 'VNL',
            'name' => 'Vanilla',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createVariantType(): int
    {
        return Schema::getConnection()->table('variant_type')->insertGetId([
            'code' => 'BTL',
            'name' => 'Bottle',
        ]);
    }

    private function variantData(int $fragranceId, int $variantTypeId): array
    {
        return [
            'fragrance_id' => $fragranceId,
            'variant_type_id' => $variantTypeId,
            'bottle_size_ml' => 30,
            'base_price' => 25000,
            'cost_ml' => 500,
            'mix_ratio' => '1:2',
            'is_active' => true,
        ];
    }
}
