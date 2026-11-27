<?php

namespace Database\Factories;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kategori>
 */
class KategoriFactory extends Factory
{
    protected $model = Kategori::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kata = ucfirst(fake()->unique()->word()).' '.fake()->unique()->numberBetween(10, 999);

        return [
            'nama_kategori' => mb_substr($kata, 0, 50),
        ];
    }
}
