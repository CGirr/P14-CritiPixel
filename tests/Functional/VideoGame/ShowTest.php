<?php

declare(strict_types=1);

namespace App\Tests\Functional\VideoGame;

use App\Model\Entity\Review;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ShowTest extends FunctionalTestCase
{
    public function testShouldShowVideoGame(): void
    {
        $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Jeu vidéo 0');
    }

    public function testShouldPostReview(): void
    {
        $this->login();
        $crawler = $this->get('/jeu-video-0');
        self::assertResponseIsSuccessful();

        // Get review form
        $submitButton = $crawler->selectButton('Poster');
        $form = $submitButton->form();
        $form['review[rating]'] = 4;
        $form['review[comment]'] = 'Ce jeu est presque parfait !';

        // Post review form
        $this->client->submit($form);

        // Check HTTP status and follow redirect
        self::assertResponseRedirects('/jeu-video-0', Response::HTTP_FOUND);
        $this->client->followRedirect();

        // Check database values
        $review = $this->getEntityManager()
            ->getRepository(Review::class)
            ->findOneBy(['comment' => 'Ce jeu est presque parfait !']);

        self::assertNotNull($review);
        self::assertEquals(4, $review->getRating());
        self::assertEquals('Ce jeu est presque parfait !', $review->getComment());

        // Check form not available
        self::assertSelectorTextNotContains('button', 'Poster');
    }

    public function testShouldNotPostReviewWithInvalidRating(): void
    {
        $this->login();
        $crawler = $this->get('/jeu-video-0');

        // Get review form
        $submitButton = $crawler->selectButton('Poster');
        $form = $submitButton->form();
        $form['review[rating]']->disableValidation();
        $form['review[rating]'] = 6;
        $form['review[comment]'] = 'Ce jeu est presque parfait !';

        // Post review form
        $this->client->submit($form);

        // Check HTTP status
        self::assertResponseIsUnprocessable();
    }

    public function testShouldNotPostReviewWhenNotAuthenticated(): void
    {
        $this->get('/jeu-video-0');

        self::assertSelectorNotExists('button:contains("Poster")');

        // Try to post
        $this->client->request('POST', '/jeu-video-0', [
            'review' => [
                'rating' => 4,
                'comment' => 'Ce jeu est presque parfait !',
            ],
        ]);

        self::assertResponseStatusCodeSame(422);
    }
}
