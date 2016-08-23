<?php

namespace Tests\AppBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class WebsiteRepositoryTest extends KernelTestCase {
	/*
	 * @var \Doctrine\ORM\EntityManager
	 */
	private $em;

	protected function setUp() {
		self::bootKernel();

		$this->em = static::$kernel->getContainer()
		                           ->get( 'doctrine' )
		                           ->getManager();
	}

	public function testGetWebsitesCount()
	{
			$count = $this->em
					->getRepository('AppBundle:Website')
					->getWebsitesCount();

			$this->assertEquals(1218, $count);
	}

	public function testGetRandomWebsite()
	{
			$website = $this->em
					->getRepository('AppBundle:Website')
					->findRandomWebsite();

			$this->assertArrayHasKey('url', array_pop($website));
	}

	protected function tearDown()
	{
		parent::tearDown();

		$this->em->close();
		$this->em = null;
	}
}
