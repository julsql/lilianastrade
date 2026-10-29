<?php

namespace App\Tests\Functional;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginControllerTest extends DatabaseWebTestCase
{
    public function testLoginFormAuthenticatesWithCsrfToken(): void
    {
        $user = $this->createMerchant('owner')->getUser();
        $user->setPassword(static::getContainer()->get(UserPasswordHasherInterface::class)->hashPassword($user, 'secret'));
        $this->em->flush();

        $this->client->request('GET', '/login');
        $this->client->submitForm('Connexion', ['_username' => 'owner@example.com', '_password' => 'secret']);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.navbar-nav', 'Déconnexion');
    }

    public function testLoginFailsWithoutValidCsrfToken(): void
    {
        $this->createMerchant('owner');

        $this->client->request('POST', '/login', ['_username' => 'owner@example.com', '_password' => 'unused', '_csrf_token' => 'forged']);
        $this->client->followRedirect();

        $this->assertSelectorNotExists('.navbar-nav a[href="/logout"]');
    }
}
