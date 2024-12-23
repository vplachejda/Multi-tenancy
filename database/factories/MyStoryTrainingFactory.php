<?php

namespace Database\Factories;

use App\Models\MyStoryTraining;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MyStoryTraining>
 */
class MyStoryTrainingFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = MyStoryTraining::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'training_date' => $this->faker->date(),
            'user_id' => User::factory(), 
        ];
    }
}
