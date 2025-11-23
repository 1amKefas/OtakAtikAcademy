<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_admin' => false,
            'is_instructor' => false,
            'age_range' => fake()->randomElement(['18-24', '25-34', '35-44', '45-54', '55+']),
            'education_level' => fake()->randomElement(['High School', 'Bachelor', 'Master', 'Doctorate', 'Other']),
            'location' => fake()->city(),
            'phone' => fake()->phoneNumber(),
            'date_of_birth' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'bio' => fake()->paragraph(),
            'expertise' => fake()->randomElement(['Web Development', 'Data Science', 'Digital Marketing', 'UI/UX Design', 'Mobile Development']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => true,
            'is_instructor' => false,
            'name' => 'Admin ' . fake()->firstName(),
        ]);
    }

    /**
     * Indicate that the user is an instructor.
     */
    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_admin' => false,
            'is_instructor' => true,
            'name' => fake()->firstName() . ' ' . fake()->lastName() . ' (Instructor)',
            'bio' => fake()->paragraph(3),
            'expertise' => fake()->randomElement([
                'Web Development', 
                'Data Science', 
                'Digital Marketing', 
                'UI/UX Design', 
                'Mobile Development',
                'Backend Development',
                'Frontend Development',
                'Fullstack Development'
            ]),
        ]);
    }

    /**
     * Indicate that the user has specific attributes.
     */
    public function withDetails(array $details): static
    {
        return $this->state(fn (array $attributes) => $details);
    }
}