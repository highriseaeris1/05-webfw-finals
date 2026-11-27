<?php

namespace Database\Factories;

use App\Enums\StatusBarang;
use App\Models\Barang;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Barang>
 */
class BarangFactory extends Factory
{
    protected $model = Barang::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kategori_id' => Kategori::factory(),
            'nama_barang' => ucfirst(fake()->words(2, true)),
            'nama_penemu' => fake()->name(),
            'tanggal_ditemukan' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'deskripsi' => fake()->sentence(8),
            'foto' => null,
            'status' => StatusBarang::Tersedia->value,
            'created_at' => now(),
        ];
    }

    public function tersedia(): static
    {
        return $this->state(fn (): array => [
            'status' => StatusBarang::Tersedia->value,
        ]);
    }

    public function diajukan(): static
    {
        return $this->state(fn (): array => [
            'status' => StatusBarang::Diajukan->value,
        ]);
    }

    public function terambil(): static
    {
        return $this->state(fn (): array => [
            'status' => StatusBarang::Terambil->value,
        ]);
    }

    public function dimusnahkan(): static
    {
        return $this->state(fn (): array => [
            'status' => StatusBarang::Dimusnahkan->value,
        ]);
    }

    /**
     * Mundurkan tanggal ditemukan untuk simulasi umur penyimpanan barang.
     */
    public function berumurTahun(int $tahun): static
    {
        return $this->state(fn (): array => [
            'tanggal_ditemukan' => now()->subYears($tahun)->subDays(10)->toDateString(),
        ]);
    }
}
