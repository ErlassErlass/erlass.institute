<?php

namespace Database\Factories;

use App\Models\Salesman;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Salesman>
 */
class SalesmanFactory extends Factory
{
    protected $model = Salesman::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'kode_salesman' => 'SLS-' . $this->faker->unique()->numberBetween(100, 999),
            'nama_salesman' => $this->faker->name(),
            'group_leader' => $this->faker->name(),
            'area' => 'JAKARTA',
        ];
    }
}
