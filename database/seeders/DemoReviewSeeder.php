<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Database\Seeder;

class DemoReviewSeeder extends Seeder
{
    public function run(): void
    {
        if (Review::count() >= 30) {
            $this->command->info('✓ Reviews already exist. Skipping.');
            return;
        }

        $reviewTemplates = [
            5 => [
                'Absolutely love this product! Exceeded all my expectations. Highly recommended to everyone.',
                'Best purchase I have made this year. Quality is outstanding and delivery was super fast.',
                'Fantastic value for money. The product is exactly as described. Very satisfied!',
                'Amazing quality! I have been using it for a week now and it works perfectly. 5 stars!',
                'Exceeded my expectations. Premium quality and looks even better in person.',
                'Perfect! Fast shipping, great packaging, and the product is top-notch.',
            ],
            4 => [
                'Great product overall. Minor issues but nothing major. Would recommend.',
                'Good quality and works as expected. Shipping took a bit longer than expected.',
                'Very satisfied with the purchase. Only wish it came in more colors.',
                'Solid product for the price. Does exactly what it promises.',
            ],
            3 => [
                'Decent product. Not exceptional but does the job. Average quality.',
                'Okay for the price. Nothing special but not bad either.',
                'Product is fine but packaging could be better.',
            ],
            2 => [
                'Not quite what I expected. Quality is below average.',
                'Disappointed with the product. Delivery was okay but product quality is poor.',
            ],
            1 => [
                'Very disappointed. Product arrived damaged and does not work properly.',
            ],
        ];

        $products = Product::active()->take(15)->get();
        $users    = User::role('user')->get();
        $service  = app(ReviewService::class);

        if ($users->isEmpty()) {
            $this->command->warn('No customers found. Skipping reviews.');
            return;
        }

        $totalReviews = 0;

        foreach ($products as $product) {
            // Each product gets 3-8 reviews
            $reviewCount = rand(3, 8);
            $shuffledUsers = $users->shuffle()->take($reviewCount);

            foreach ($shuffledUsers as $user) {
                // Skip if user already reviewed (unique constraint)
                if ($service->hasReviewed($user, $product)) {
                    continue;
                }

                // Rating weighted towards higher
                $rating = $this->weightedRating();

                $comments = $reviewTemplates[$rating] ?? $reviewTemplates[5];
                $comment = $comments[array_rand($comments)];

                try {
                    $review = $service->addReview($user, $product, [
                        'rating'  => $rating,
                        'title'   => $this->randomTitle($rating),
                        'comment' => $comment,
                    ]);

                    // Randomly set some reviews as "edited"
                    if (rand(1, 10) === 1) {
                        $review->update(['edited_at' => now()->subDays(rand(1, 7))]);
                    }

                    $totalReviews++;
                } catch (\Throwable $e) {
                    // Skip on error
                    continue;
                }
            }
        }

        $this->command->info("✓ Demo reviews seeded: {$totalReviews}");
    }

    protected function weightedRating(): int
    {
        // 60% chance of 5★, 25% of 4★, 10% of 3★, 3% of 2★, 2% of 1★
        $rand = rand(1, 100);

        if ($rand <= 60) return 5;
        if ($rand <= 85) return 4;
        if ($rand <= 95) return 3;
        if ($rand <= 98) return 2;
        return 1;
    }

    protected function randomTitle(int $rating): string
    {
        $titles = [
            5 => ['Excellent!', 'Perfect product!', 'Highly recommended', 'Amazing quality', 'Just what I needed'],
            4 => ['Very good', 'Great value', 'Satisfied customer', 'Good purchase'],
            3 => ['Decent', 'Average product', 'Okay'],
            2 => ['Not great', 'Could be better'],
            1 => ['Disappointed', 'Poor quality'],
        ];

        $options = $titles[$rating] ?? ['Great product'];
        return $options[array_rand($options)];
    }
}