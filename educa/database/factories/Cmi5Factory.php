<?php

namespace Database\Factories;

use App\Models\Cmi5;
use Illuminate\Database\Eloquent\Factories\Factory;

class Cmi5Factory extends Factory
{
    protected $model = Cmi5::class;

    public function definition()
    {
        return [
            'title' => $this->faker->words(3, true),
            'iri' => $this->faker->url(),
        ];
    }
}
