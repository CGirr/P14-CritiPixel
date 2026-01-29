<?php

namespace App\Tests\Unit;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class CalculateAverageRatingTest extends TestCase
{
    public function testCalculateAverageRatingWithNoReviews(): void
    {
        // Create a video game without review
        $videoGame = new VideoGame();
        $ratingHandler = new RatingHandler();

        // Calculate average
        $ratingHandler->calculateAverage($videoGame);

        // Assert average is null
        $this->assertNull($videoGame->getAverageRating());
    }

    public function testCalculateAverageRatingWithOneReview(): void
    {
        // Create a video game with a review
        $videoGame = new VideoGame();
        $review = (new Review())
            ->setRating(5)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review);
        $ratingHandler = new RatingHandler();

        // Calculate average
        $ratingHandler->calculateAverage($videoGame);

        // Assert average equals 5
        $this->assertEquals(5, $videoGame->getAverageRating());
    }

    public function testCalculateAverageRatingWithMultipleReviews(): void
    {
        $videoGame = new VideoGame();
        $review1 = (new Review())
            ->setRating(5)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review1);

        $review2 = (new Review())
            ->setRating(4)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review2);

        $review3 = (new Review())
            ->setRating(3)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review3);

        $ratingHandler = new RatingHandler();

        $ratingHandler->calculateAverage($videoGame);

        $this->assertEquals(4, $videoGame->getAverageRating());
    }

    public function testCalculateAverageRatingWithRounding(): void
    {
        $videoGame = new VideoGame();
        $review1 = (new Review())
            ->setRating(1)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review1);

        $review2 = (new Review())
            ->setRating(4)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review2);

        $review3 = (new Review())
            ->setRating(3)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review3);

        $ratingHandler = new RatingHandler();

        $ratingHandler->calculateAverage($videoGame);

        $this->assertEquals(3, $videoGame->getAverageRating());
    }
}
