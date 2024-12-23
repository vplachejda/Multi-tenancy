<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\LearningPathMedal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LearningPathMedal>
 */
class LearningPathMedalFactory extends Factory
{
    /**
    * The name of the factory's corresponding model.
    *
    * @var string
    */
    protected $model = LearningPathMedal::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(), 
            'medal_name' => $this->faker->word, 
            'description' => $this->faker->sentence, 
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
