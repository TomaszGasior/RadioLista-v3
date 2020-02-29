<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testUserPublicProfileDescription(): void
    {
        $crawler = $this->client->request('GET', '/ustawienia-konta', [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $exampleContent = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>';

        $form = $crawler->filter('form')->form();
        $form['user_settings[aboutMe]'] = $exampleContent;
        $form['user_settings[publicProfile]'] = '1';
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/profil/test_user', [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $content = $crawler->html();
        $this->assertStringContainsString($exampleContent, $content);
    }

    public function testUserPublicProfileVisibility(): void
    {
        $setPublicProfile = function(bool $enabled) {
            $crawler = $this->client->request('GET', '/ustawienia-konta', [], [], [
                'PHP_AUTH_USER' => 'test_user',
                'PHP_AUTH_PW' => 'test_user',
            ]);

            $form = $crawler->filter('form')->form();
            $form['user_settings[publicProfile]'] = '1';
            if (false === $enabled) {
                unset($form['user_settings[publicProfile]']);
            }

            $this->client->submit($form);

            $this->client->restart();
        };

        $setPublicProfile(true);
        $this->client->request('GET', '/profil/test_user');
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $setPublicProfile(false);
        $this->client->request('GET', '/profil/test_user');
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testChangeUserPassword(): void
    {
        $newPassword = 'MEEEEEEEEEEEEEEEEEEEEEEEEH';

        $crawler = $this->client->request('GET', '/ustawienia-konta', [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $form = $crawler->filter('form')->form();
        $form['user_settings[currentPassword]'] = 'test_user';
        $form['user_settings[plainPassword][first]'] = $newPassword;
        $form['user_settings[plainPassword][second]'] = $newPassword;
        $this->client->submit($form);

        $this->client->restart();

        $this->client->request('GET', '/ustawienia-konta', [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => $newPassword,
        ]);
        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());

        $this->client->restart();

        $this->client->request('GET', '/ustawienia-konta', [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        /** @var RedirectResponse */
        $response = $this->client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/logowanie', $response->getTargetUrl());
    }

    public function testDontChangeUserPasswordWhenCurrentInvalid(): void
    {
        $newPassword = 'MEEEEEEEEEEEEEEEEEEEEEEEEH';

        $crawler = $this->client->request('GET', '/ustawienia-konta', [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);

        $form = $crawler->filter('form')->form();
        $form['user_settings[currentPassword]'] = 'invalid_current_pass';
        $form['user_settings[plainPassword][first]'] = $newPassword;
        $form['user_settings[plainPassword][second]'] = $newPassword;
        $this->client->submit($form);

        $this->client->restart();

        $this->client->request('GET', '/ustawienia-konta', [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => $newPassword,
        ]);
        /** @var RedirectResponse */
        $response = $this->client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/logowanie', $response->getTargetUrl());

        $this->client->restart();

        $this->client->request('GET', '/ustawienia-konta', [], [], [
            'PHP_AUTH_USER' => 'test_user',
            'PHP_AUTH_PW' => 'test_user',
        ]);
        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }

    public function testRegisterNewUser(): void
    {
        $crawler = $this->client->request('GET', '/rejestracja');

        $form = $crawler->filter('form')->form();
        $form['user_register[name]'] = 'EXAMPLE_LOGIN';
        $form['user_register[plainPassword][first]'] = 'EXAMPLE_PASSW0RD!';
        $form['user_register[plainPassword][second]'] = 'EXAMPLE_PASSW0RD!';
        $form['user_register[acceptServiceTerms]'] = '1';
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/logowanie');

        $form = $crawler->filter('form')->form();
        $form['security_login[username]'] = 'EXAMPLE_LOGIN';
        $form['security_login[password]'] = 'EXAMPLE_PASSW0RD!';
        $this->client->submit($form);

        /** @var RedirectResponse */
        $response = $this->client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/moje-wykazy', $response->getTargetUrl());

        $crawler = $this->client->request('GET', '/moje-wykazy');

        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }
}
