<?php

namespace Database\Factories;

use App\Models\RequestStat;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestStatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RequestStat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'created_at' => $this->faker->dateTimeBetween('-2 days', 'now', 'Europe/Stockholm'),
            'updated_at' => fn (array $attr) => $attr['created_at'],
        ];
    }
}
