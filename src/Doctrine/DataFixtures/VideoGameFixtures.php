<?php

namespace App\Doctrine\DataFixtures;

use App\Model\Entity\Review;
use App\Model\Entity\Tag;
use App\Model\Entity\User;
use App\Model\Entity\VideoGame;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();

        $videoGames = \array_fill_callback(0, 50, fn (int $index): VideoGame => (new VideoGame())
            ->setTitle(sprintf('Jeu vidéo %d', $index))
            ->setDescription($this->faker->paragraphs(10, true))
            ->setReleaseDate(new \DateTimeImmutable())
            ->setTest($this->faker->paragraphs(6, true))
            ->setRating(($index % 5) + 1)
            ->setImageName(sprintf('video_game_%d.png', $index))
            ->setImageSize(2_098_872)
        );

        $tags = \array_fill_callback(0, 5, fn (): Tag => (new Tag())
            ->setName($this->faker->word())
        );

        array_walk($videoGames, function (VideoGame $videoGame) use ($users, $manager) {
            $numberOfReviews = rand(0, 3);

            for ($i = 0; $i < $numberOfReviews; ++$i) {
                $review = (new Review())
                    ->setComment($this->faker->paragraph())
                    ->setRating(rand(1, 5))
                    ->setVideoGame($videoGame)
                    ->setUser($users[array_rand($users)]);

                $manager->persist($review);
            }
        });

        array_walk($tags, [$manager, 'persist']);

        array_walk($videoGames, function (VideoGame $videoGame) use ($tags) {
            if ($this->faker->boolean(90)) {
                $numberOfTags = rand(1, 3);
                $randomKeys = (array) array_rand($tags, $numberOfTags);

                foreach ($randomKeys as $randomKey) {
                    $videoGame->getTags()->add($tags[$randomKey]);
                }
            }
        });

        array_walk($videoGames, [$manager, 'persist']);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
