<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\LearningPathUser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LearningPathUser>
 */
class LearningPathUserFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = LearningPathUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'path_id' => $this->faker->word,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
