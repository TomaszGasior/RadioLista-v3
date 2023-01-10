<?php

namespace App\Tests\Controller;

use App\Tests\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /** @var KernelBrowser */
    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function test_user_can_update_description_in_his_public_profile(): void
    {
        $this->client->loginUserByName('test_user');
        $crawler = $this->client->request('GET', '/ustawienia-konta');

        $exampleContent = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.</p>';

        $form = $crawler->filter('form')->form();
        $form['user_settings[aboutMe]'] = $exampleContent;
        $form['user_settings[publicProfile]'] = '1';
        $this->client->submit($form);

        $crawler = $this->client->request('GET', '/profil/test_user');
        $content = $crawler->html();
        $this->assertStringContainsString($exampleContent, $content);
    }

    public function test_user_can_enable_and_disable_his_public_profile(): void
    {
        $setPublicProfile = function(bool $enabled): void {
            $this->client->loginUserByName('test_user');

            $crawler = $this->client->request('GET', '/ustawienia-konta');

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

    public function test_user_can_change_his_password(): void
    {
        $newPassword = 'MEEEEEEEEEEEEEEEEEEEEEEEEH';

        $this->client->loginUserByName('test_user');
        $crawler = $this->client->request('GET', '/ustawienia-konta');

        $form = $crawler->filter('form')->form();
        $form['user_settings[currentPassword]'] = 'test_user';
        $form['user_settings[plainPassword][first]'] = $newPassword;
        $form['user_settings[plainPassword][second]'] = $newPassword;
        $this->client->submit($form);

        $this->client->restart();

        $this->loginUserThroughForm('test_user', $newPassword);
        $this->client->request('GET', '/ustawienia-konta');
        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());

        $this->client->restart();

        $this->loginUserThroughForm('test_user', 'test_user!');
        $this->client->request('GET', '/ustawienia-konta');
        /** @var RedirectResponse */
        $response = $this->client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/logowanie', $response->getTargetUrl());
    }

    public function test_user_cannot_change_his_password_when_provides_invalid_current_password(): void
    {
        $newPassword = 'MEEEEEEEEEEEEEEEEEEEEEEEEH';

        $this->client->loginUserByName('test_user');
        $crawler = $this->client->request('GET', '/ustawienia-konta');

        $form = $crawler->filter('form')->form();
        $form['user_settings[currentPassword]'] = 'invalid_current_pass';
        $form['user_settings[plainPassword][first]'] = $newPassword;
        $form['user_settings[plainPassword][second]'] = $newPassword;
        $this->client->submit($form);

        $this->client->restart();

        $this->loginUserThroughForm('test_user', $newPassword);
        $this->client->request('GET', '/ustawienia-konta');
        /** @var RedirectResponse */
        $response = $this->client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/logowanie', $response->getTargetUrl());

        $this->client->restart();

        $this->loginUserThroughForm('test_user', 'test_user');
        $this->client->request('GET', '/ustawienia-konta');
        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_user_can_register_new_account(): void
    {
        $crawler = $this->client->request('GET', '/rejestracja');

        $form = $crawler->filter('form')->form();
        $form['user_register[name]'] = 'EXAMPLE_LOGIN';
        $form['user_register[plainPassword][first]'] = 'EXAMPLE_PASSW0RD!';
        $form['user_register[plainPassword][second]'] = 'EXAMPLE_PASSW0RD!';
        $form['user_register[acceptServiceTerms]'] = '1';
        $this->client->submit($form);

        $this->loginUserThroughForm('EXAMPLE_LOGIN', 'EXAMPLE_PASSW0RD!');

        /** @var RedirectResponse */
        $response = $this->client->getResponse();
        $this->assertSame(302, $response->getStatusCode());
        $this->assertSame('http://localhost/moje-wykazy', $response->getTargetUrl());

        $crawler = $this->client->request('GET', '/moje-wykazy');

        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
    }

    private function loginUserThroughForm(string $username, $password): void
    {
        $crawler = $this->client->request('GET', '/logowanie');

        $form = $crawler->filter('form')->form();
        $form['security_login[username]'] = $username;
        $form['security_login[password]'] = $password;

        $this->client->submit($form);
    }
}
