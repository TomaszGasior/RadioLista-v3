<?php

namespace App\Tests\Functional;

use App\Entity\RadioTable;
use App\Repository\RadioTableRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RadioTableStatusTest extends WebTestCase
{
    /**
     * @dataProvider statusAndHttpCodeProvider
     */
    public function testRadioTablePublicAccess(RadioTable $radioTable, $status,
                                               int $expectedHttpCode,
                                               bool $expectedNoIndexTag): void
    {
        $this->setRadioTableStatus($radioTable, $status);

        $client = static::createClient();
        $crawler = $client->request('GET', '/wykaz/' . $radioTable->getId());

        $this->assertSame($expectedHttpCode, $client->getResponse()->getStatusCode());

        $robotsTag = $crawler->filter('meta[name="robots"]');
        if ($expectedNoIndexTag) {
            $this->assertContains('noindex', $robotsTag->attr('content'));
        }
        else {
            $this->assertCount(0, $robotsTag);
        }

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user_2',
            'PHP_AUTH_PW' => 'test_user_2',
        ]);
        $client->request('GET', '/wykaz/' . $radioTable->getId());

        $this->assertSame($expectedHttpCode, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider statusAndHttpCodeProvider
     */
    public function testOwnerAlwaysHasAccess(RadioTable $radioTable, $status): void
    {
        $this->setRadioTableStatus($radioTable, $status);

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $client->request('GET', '/wykaz/' . $radioTable->getId());

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function statusAndHttpCodeProvider(): array
    {
        $radioTable = $this->getRadioTable();

        return [
            [$radioTable, RadioTable::STATUS_PUBLIC, 200, false],
            [$radioTable, RadioTable::STATUS_UNLISTED, 200, true],
            [$radioTable, RadioTable::STATUS_PRIVATE, 404, true],
        ];
    }

    /**
     * @dataProvider statusAndAnchorVisibilityProvider
     */
    public function testVisibilityInAllRadioTablesList(RadioTable $radioTable, $status,
                                                       bool $expectedVisibleAnchor): void
    {
        $this->setRadioTableStatus($radioTable, $status);

        $client = static::createClient();
        $crawler = $client->request('GET', '/wszystkie-wykazy');

        $radioTableUrl = '/wykaz/' . $radioTable->getId();
        $anchors = $crawler->filter('a[href="' . $radioTableUrl . '"]');

        $this->assertCount($expectedVisibleAnchor ? 1 : 0, $anchors);
    }

    /**
     * @dataProvider statusAndAnchorVisibilityProvider
     */
    public function testVisibilityInOwnerPublicProfile(RadioTable $radioTable, $status,
                                                       bool $expectedVisibleAnchor): void
    {
        $this->setRadioTableStatus($radioTable, $status);
        $this->enableUserPublicProfile();

        $client = static::createClient();
        $crawler = $client->request('GET', '/profil/test_user');

        $radioTableUrl = '/wykaz/' . $radioTable->getId();
        $anchors = $crawler->filter('a[href="' . $radioTableUrl . '"]');

        $this->assertCount($expectedVisibleAnchor ? 1 : 0, $anchors);
    }

    public function statusAndAnchorVisibilityProvider(): array
    {
        $radioTable = $this->getRadioTable();

        return [
            [$radioTable, RadioTable::STATUS_PUBLIC, true],
            [$radioTable, RadioTable::STATUS_UNLISTED, false],
            [$radioTable, RadioTable::STATUS_PRIVATE, false],
        ];
    }

    private function getRadioTable(): RadioTable
    {
        self::bootKernel();
        $userRepository = self::$container->get(UserRepository::class);
        $radioTableRepository = self::$container->get(RadioTableRepository::class);

        $user = $userRepository->findOneByName('test_user');
        $radioTable = $radioTableRepository->findAllOwnedByUser($user)[0];

        return $radioTable;
    }

    private function setRadioTableStatus(RadioTable $radioTable, $newStatus): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $crawler = $client->request('GET', '/wykaz/' . $radioTable->getId() . '/ustawienia');

        $form = $crawler->filter('form')->form();
        $form['radio_table_settings[status]'] = $newStatus;
        $client->submit($form);
    }

    private function enableUserPublicProfile(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $crawler = $client->request('GET', '/ustawienia-konta');

        $form = $crawler->filter('form')->form();
        $form['user_settings[publicProfile]'] = '1';

        $client->submit($form);
    }
}
