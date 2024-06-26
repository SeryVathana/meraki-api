<?php

namespace Database\Factories;

use App\Model\User;
use App\Model\Post;
use App\Model\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SavedPost>
 */
class SavedPostFactory extends Factory
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
            'post_id' => Post::factory(),
            'folder_id' => Folder::factory(),
        ];
    }
}
