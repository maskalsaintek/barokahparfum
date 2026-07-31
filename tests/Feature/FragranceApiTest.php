<?php

namespace Tests\Feature;

use App\Models\Fragrance;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FragranceApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('fragrance', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->string('gender')->nullable();
            $table->text('description')->nullable();
            $table->string('origin', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function test_it_creates_lists_shows_updates_and_deletes_a_fragrance(): void
    {
        $created = $this->postJson('/api/fragrances', [
            'code' => 'OUD',
            'name' => 'Arabian Oud',
            'origin' => 'Middle East',
            'is_active' => true,
        ])
            ->assertCreated()
            ->assertJsonPath('code', 'OUD')
            ->assertJsonPath('gender', 'UNISEX')
            ->json();

        $this->getJson('/api/fragrances?name=Arabian&code=OU&origin=Middle')
            ->assertOk()
            ->assertJsonPath('data.0.id', $created['id'])
            ->assertJsonStructure(['data', 'current_page', 'last_page', 'per_page', 'total']);

        Fragrance::create([
            'code' => 'VNL',
            'name' => 'Vanilla',
            'gender' => 'UNISEX',
        ]);

        $this->getJson('/api/fragrances?limit=1&offset=1')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.limit', 1)
            ->assertJsonPath('meta.offset', 1)
            ->assertJsonPath('meta.next_offset', 2)
            ->assertJsonPath('meta.has_more', false);

        $this->getJson('/api/fragrances/'.$created['id'])
            ->assertOk()
            ->assertJsonPath('id', $created['id']);

        $this->patchJson('/api/fragrances/'.$created['id'], [
            'name' => 'Arabian Oud Intense',
            'gender' => 'MALE',
        ])
            ->assertOk()
            ->assertJsonPath('name', 'Arabian Oud Intense')
            ->assertJsonPath('gender', 'MALE');

        $this->deleteJson('/api/fragrances/'.$created['id'])->assertNoContent();
        $this->assertDatabaseMissing('fragrance', ['id' => $created['id']]);
    }

    public function test_it_validates_fragrance_payloads(): void
    {
        Fragrance::create([
            'code' => 'VNL',
            'name' => 'Vanilla',
            'gender' => 'UNISEX',
        ]);

        $this->postJson('/api/fragrances', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['code', 'name']);

        $this->postJson('/api/fragrances', [
            'code' => 'VNL',
            'name' => 'Vanilla Duplicate',
            'gender' => 'UNKNOWN',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['code', 'gender']);
    }
}
