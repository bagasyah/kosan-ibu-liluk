<?php

namespace Database\Factories;

use App\Models\Kamar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Kamar>
 */
class KamarFactory extends Factory
{
    protected $model = Kamar::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'no_kamar' => $this->faker->unique()->numberBetween(100, 999),
            'indekos_id' => \App\Models\Indekos::factory(),
            // Tambahkan atribut lain yang diperlukan
        ];
    }
}
