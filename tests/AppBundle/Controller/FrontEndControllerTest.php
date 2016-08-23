<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontEndControllerTest extends WebTestCase
{
		public function testIndex()
		{
			$client = static::createClient();
			$crawler = $client->request('GET', '/');

			$this->assertEquals(200, $client->getResponse()->getStatusCode());
		}
}
