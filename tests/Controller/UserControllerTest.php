<?php

namespace App\Tests\Controller;

use App\Entity\RadioTable;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testUserPublicProfileDescription(): void
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $crawler = $client->request('GET', '/ustawienia-konta');

        $exampleContent = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>';

        $form = $crawler->filter('form')->form();
        $form['user_settings[aboutMe]'] = $exampleContent;
        $form['user_settings[publicProfile]'] = '1';
        $client->submit($form);

        $crawler = $client->request('GET', '/profil/test_user');
        $content = $crawler->html();
        $this->assertContains($exampleContent, $content);
    }

    public function testUserPublicProfileVisibility(): void
    {
        $client = static::createClient();

        $setPublicProfile = function(bool $enabled) use ($client) {
            $crawler = $client->request('GET', '/ustawienia-konta', [], [], [
                'PHP_AUTH_USER' => 'test_user',
                'PHP_AUTH_PW' => 'test_user',
            ]);

            $form = $crawler->filter('form')->form();
            $form['user_settings[publicProfile]'] = '1';
            if (false === $enabled) {
                unset($form['user_settings[publicProfile]']);
            }

            $client->submit($form);
        };

        $setPublicProfile(true);
        $client->request('GET', '/profil/test_user');
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        $setPublicProfile(false);
        $client->request('GET', '/profil/test_user');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }

    public function testChangeUserPassword(): void
    {
        $newPassword = 'MEEEEEEEEEEEEEEEEEEEEEEEEH';

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $crawler = $client->request('GET', '/ustawienia-konta');

        $form = $crawler->filter('form')->form();
        $form['user_settings[currentPassword]'] = 'test_user';
        $form['user_settings[plainPassword][first]'] = $newPassword;
        $form['user_settings[plainPassword][second]'] = $newPassword;
        $client->submit($form);

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => $newPassword,
        ]);
        $client->request('GET', '/ustawienia-konta');
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $client->request('GET', '/ustawienia-konta');
        $response = $client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/logowanie', $response->getTargetUrl());
    }

    public function testDontChangeUserPasswordWhenCurrentInvalid(): void
    {
        $newPassword = 'MEEEEEEEEEEEEEEEEEEEEEEEEH';

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $crawler = $client->request('GET', '/ustawienia-konta');

        $form = $crawler->filter('form')->form();
        $form['user_settings[currentPassword]'] = 'invalid_current_pass';
        $form['user_settings[plainPassword][first]'] = $newPassword;
        $form['user_settings[plainPassword][second]'] = $newPassword;
        $client->submit($form);

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => $newPassword,
        ]);
        $client->request('GET', '/ustawienia-konta');
        $response = $client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/logowanie', $response->getTargetUrl());

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $client->request('GET', '/ustawienia-konta');
        $response = $client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }
}
