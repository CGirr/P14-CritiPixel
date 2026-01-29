<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\Tag;
use App\Model\Entity\VideoGame;
use App\Tests\Functional\FunctionalTestCase;

final class FilterTest extends FunctionalTestCase
{
    public function testShouldListTenVideoGames(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->clickLink('2');
        self::assertResponseIsSuccessful();
    }

    public function testShouldFilterVideoGamesBySearch(): void
    {
        $this->get('/');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(10, 'article.game-card');
        $this->client->submitForm('Filtrer', ['filter[search]' => 'Jeu vidéo 49'], 'GET');
        self::assertResponseIsSuccessful();
        self::assertSelectorCount(1, 'article.game-card');
    }

    /**
     * @dataProvider filterDataProvider
     */
    public function testShouldFilterVideoGamesByTag(int $numberOfTags): void
    {
        // Get all tags from database
        $tags = $this->getEntityManager()
            ->getRepository(Tag::class)
            ->findAll();

        // Construct tagIds array
        $tagIds = [];
        for ($i = 0; $i < $numberOfTags; ++$i) {
            $tagIds[] = $tags[$i]->getId();
        }

        // Construct filter parameters
        $parameters = $numberOfTags > 0
            ? ['filter[tags]' => $tagIds, 'limit' => 999]
            : ['limit' => 999];

        // Load page and submit form
        $this->get('/');
        $this->client->submitForm('Filtrer', $parameters, 'GET');

        // Check response
        self::assertResponseIsSuccessful();

        // Count expected games matching all selected tags
        $allGames = $this->getEntityManager()
            ->getRepository(VideoGame::class)
            ->findAll();

        $expectedCount = 0;

        foreach ($allGames as $game) {
            $gameTagIds = [];
            foreach ($game->getTags() as $tag) {
                $gameTagIds[] = $tag->getId();
            }

            $missingTags = array_diff($tagIds, $gameTagIds);

            if (empty($missingTags)) {
                ++$expectedCount;
            }
        }

        // Assert displayed count matches expected count
        self::assertSelectorCount($expectedCount, 'article.game-card');
    }

    public function testShouldFilterWithNonExistentTag(): void
    {
        // Load page with parameters and an invalid tag
        $this->client->request('GET', '/', [
            'page' => 1,
            'limit' => 999,
            'filter' => [
                'tags' => [999],
            ],
        ]);
        self::assertResponseIsSuccessful();

        // Assert all games are displayed
        $expectedCount = count($this->getEntityManager()->getRepository(VideoGame::class)->findAll());
        self::assertSelectorCount($expectedCount, 'article.game-card');
    }

    /**
     * @return int[][]
     */
    public static function filterDataProvider(): array
    {
        return [
            'no tags' => [0],
            'one tag' => [1],
            'two tags' => [2],
        ];
    }
}
