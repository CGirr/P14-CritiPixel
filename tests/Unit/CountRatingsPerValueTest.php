<?php

namespace App\Tests\Unit;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class CountRatingsPerValueTest extends TestCase
{
    public function testCountRatingsPerValueWithNoReviews(): void
    {
        $videoGame = new VideoGame();
        $ratingHandler = new RatingHandler();

        $ratingHandler->countRatingsPerValue($videoGame);

        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfOne());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive());
    }

    public function testCountRatingsPerValueWithReviews(): void
    {
        $videoGame = new VideoGame();
        $review = (new Review())
            ->setRating(2)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review);

        $ratingHandler = new RatingHandler();

        $ratingHandler->countRatingsPerValue($videoGame);

        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfOne());
        $this->assertEquals(1, $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive());
    }

    public function testCountRatingsPerValueWithMultipleReviews(): void
    {
        $videoGame = new VideoGame();
        $review1 = (new Review())
            ->setRating(2)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review1);

        $review2 = (new Review())
            ->setRating(3)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review2);

        $review3 = (new Review())
            ->setRating(4)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review3);

        $review4 = (new Review())
            ->setRating(3)
            ->setVideoGame($videoGame);
        $videoGame->getReviews()->add($review4);

        $ratingHandler = new RatingHandler();

        $ratingHandler->countRatingsPerValue($videoGame);

        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfOne());
        $this->assertEquals(1, $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo());
        $this->assertEquals(2, $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree());
        $this->assertEquals(1, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive());
    }
}
