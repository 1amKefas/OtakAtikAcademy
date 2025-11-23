<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseRegistration>
 */
class CourseRegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = \App\Models\User::where('is_admin', false)->where('is_instructor', false)->inRandomOrder()->first();
        $course = \App\Models\Course::where('is_active', true)->inRandomOrder()->first();
        
        $status = $this->faker->randomElement(['pending', 'paid', 'cancelled']);
        $progress = $status === 'paid' ? $this->faker->numberBetween(0, 100) : 0;
        
        $price = $course->price;
        $finalPrice = $price;
        $discountCode = null;

        if ($this->faker->boolean(30)) { // 30% chance of having discount
            $discountCode = 'PROMOPNJ';
            $finalPrice = $price * 0.9; // 10% discount
        }

        return [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'nama_lengkap' => $user->name,
            'ttl' => $this->faker->city() . ', ' . $this->faker->date('d F Y', '-20 years'),
            'tempat_tinggal' => $this->faker->city(),
            'gender' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'price' => $price,
            'final_price' => $finalPrice,
            'discount_code' => $discountCode,
            'status' => $status,
            'progress' => $progress,
            'enrolled_at' => $status === 'paid' ? $this->faker->dateTimeBetween('-30 days', 'now') : null,
            'completed_at' => $progress === 100 ? $this->faker->dateTimeBetween('-10 days', 'now') : null,
        ];
    }

    /**
     * Indicate that the registration is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'progress' => 0,
            'enrolled_at' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the registration is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'progress' => $this->faker->numberBetween(0, 100),
            'enrolled_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'completed_at' => function (array $attributes) {
                return $attributes['progress'] === 100 ? $this->faker->dateTimeBetween('-10 days', 'now') : null;
            },
        ]);
    }

    /**
     * Indicate that the registration is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'progress' => 0,
            'enrolled_at' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the registration is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'progress' => 100,
            'enrolled_at' => $this->faker->dateTimeBetween('-30 days', '-10 days'),
            'completed_at' => $this->faker->dateTimeBetween('-10 days', 'now'),
        ]);
    }
}