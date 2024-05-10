<?php

namespace Database\Factories;

use App\Model\User;
use App\Model\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GroupPost>
 */
class GroupPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'group_id' => Group::factory(),
            'title' => fake()->title(), 
            'description' => fake()->description(), 
            'img_url' => "https://i.pinimg.com/736x/2f/21/94/2f21940ee0948af25337e339d4899c36.jpg",
            'likes' => "[1, 2, 3]"
        ];
    }
}
