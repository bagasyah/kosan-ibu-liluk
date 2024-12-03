<?php

namespace Database\Factories;

use App\Models\Indekos;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Indekos>
 */
class IndekosFactory extends Factory
{
    protected $model = Indekos::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->word,
            // 'jumlah_kamar' => $this->faker->numberBetween(1, 20),
            // 'jumlah_penghuni' => $this->faker->numberBetween(0, 20),
        ];
    }
}
