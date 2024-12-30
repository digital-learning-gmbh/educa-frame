<?php

namespace Database\Factories;

use App\Models\Cmi5;
use App\Models\Cmi5Au;
use Illuminate\Database\Eloquent\Factories\Factory;

class Cmi5AuFactory extends Factory
{
    protected $model = Cmi5Au::class;

    public function definition()
    {
        return [
            'title' => $this->faker->words(3, true),
            'iri' => $this->faker->url(),
            'url' => $this->faker->url(),
            'cmi5_id' => Cmi5::factory()
        ];
    }
}
