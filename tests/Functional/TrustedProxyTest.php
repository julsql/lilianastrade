<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TrustedProxyTest extends WebTestCase
{
    public function testRedirectsKeepHttpsBehindTheIngress(): void
    {
        $client = static::createClient();

        $client->request('GET', '/card/', server: [
            'REMOTE_ADDR' => '10.42.0.12',
            'HTTP_HOST' => 'lilianastrade.julsql.fr',
            'HTTP_X_FORWARDED_PROTO' => 'https',
            'HTTP_X_FORWARDED_PORT' => '443',
        ]);

        $this->assertResponseRedirects('https://lilianastrade.julsql.fr/login');
    }

    public function testForwardedHeadersFromPublicClientsAreIgnored(): void
    {
        $client = static::createClient();

        $client->request('GET', '/card/', server: [
            'REMOTE_ADDR' => '8.8.8.8',
            'HTTP_HOST' => 'lilianastrade.julsql.fr',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ]);

        $this->assertResponseRedirects('http://lilianastrade.julsql.fr/login');
    }
}
