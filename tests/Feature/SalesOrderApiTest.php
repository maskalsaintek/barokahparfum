<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesOrderApiTest extends TestCase
{
    private int $variantId;

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
            $table->string('mix_ratio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        Schema::create('sales_order', function (Blueprint $table): void {
            $table->id();
            $table->string('order_number');
            $table->dateTime('order_date');
            $table->string('customer_name')->nullable();
            $table->decimal('total_before_discount', 15, 2);
            $table->decimal('total_discount', 15, 2);
            $table->decimal('total_tax', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('total_profit', 15, 2);
            $table->decimal('total_profit_percent', 15, 2)->nullable();
            $table->decimal('total_cost_of_goods', 15, 2);
            $table->string('payment_method');
            $table->text('notes')->nullable();
            $table->string('created_by');
            $table->timestamps();
        });
        Schema::create('sales_order_item', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_order_id');
            $table->foreignId('product_variant_id');
            $table->decimal('quantity', 15, 2);
            $table->string('uom');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('discount_percent', 15, 2);
            $table->decimal('discount_amount', 15, 2);
            $table->decimal('profit_amount', 15, 2);
            $table->decimal('profit_percent', 15, 2)->nullable();
            $table->decimal('cost_of_goods', 15, 2);
            $table->decimal('line_total', 15, 2);
            $table->boolean('is_free');
        });

        $fragranceId = DB::table('fragrance')->insertGetId([
            'code' => 'VNL', 'name' => 'Vanilla', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $variantTypeId = DB::table('variant_type')->insertGetId(['code' => 'BTL', 'name' => 'Bottle']);
        $this->variantId = DB::table('product_variant')->insertGetId([
            'fragrance_id' => $fragranceId,
            'variant_type_id' => $variantTypeId,
            'bottle_size_ml' => 30,
            'base_price' => 25000,
            'cost_ml' => 500,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_mobile_can_create_list_show_update_and_delete_sales_orders(): void
    {
        $payload = [
            'order_date' => '2026-07-18 10:00:00',
            'customer_name' => 'Mobile Customer',
            'payment_method' => 'QRIS',
            'items' => [[
                'product_variant_id' => $this->variantId,
                'quantity' => 2,
                'uom' => 'PCS',
                'unit_price' => 25000,
                'discount_percent' => 10,
            ]],
        ];

        $id = $this->postJson('/api/sales-orders', $payload)
            ->assertCreated()
            ->assertJsonPath('data.total_amount', '45000.00')
            ->assertJsonPath('data.items.0.product_variant.fragrance.name', 'Vanilla')
            ->json('data.id');

        $this->getJson('/api/sales-orders?q=Mobile')
            ->assertOk()
            ->assertJsonPath('data.0.id', $id);

        $this->getJson('/api/sales-orders/'.$id)
            ->assertOk()
            ->assertJsonPath('data.order_number', 'SO-20260718-00000001');

        $this->patchJson('/api/sales-orders/'.$id, ['payment_method' => 'CASH'])
            ->assertOk()
            ->assertJsonPath('data.payment_method', 'CASH');

        $this->deleteJson('/api/sales-orders/'.$id)->assertNoContent();
        $this->assertDatabaseMissing('sales_order', ['id' => $id]);
        $this->assertDatabaseMissing('sales_order_item', ['sales_order_id' => $id]);
    }

    public function test_it_rejects_invalid_order_and_excessive_discount(): void
    {
        $this->postJson('/api/sales-orders', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['payment_method', 'items']);

        $this->postJson('/api/sales-orders', [
            'payment_method' => 'CASH',
            'items' => [[
                'product_variant_id' => $this->variantId,
                'quantity' => 1,
                'uom' => 'PCS',
                'unit_price' => 10000,
                'discount_amount' => 11000,
            ]],
        ])->assertUnprocessable()->assertJsonValidationErrors('items.0.discount_amount');
    }
}
