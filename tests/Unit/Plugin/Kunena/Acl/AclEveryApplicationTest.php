<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Tests
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Tests\Unit\Plugin\Kunena\Acl;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Kunena\Forum\Plugin\Kunena\Acl\Extension\Acl;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The access of the ranks (plugin Kunena - ACL) is given on the console and in the REST API too, as the access of Joomla: the
 * scheduled tasks check the rights of the users there (the e-mails of the queue, the scheduled replies). Without it nobody could read
 * a category of the ranks there: its e-mails were dropped, and its scheduled replies were never published.
 */
class AclEveryApplicationTest extends UnitTestCase
{
    private mixed $application;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requireJoomla();
        $this->application = Factory::$application;
    }

    protected function tearDown(): void
    {
        if (\defined('JVERSION')) {
            Factory::$application = $this->application;
        }

        parent::tearDown();
    }

    public function testTheAccessIsGivenInEveryApplication(): void
    {
        foreach (['site', 'administrator', 'cli', 'api'] as $client) {
            $application = $this->createStub(CMSApplicationInterface::class);
            $application->method('isClient')->willReturnCallback(static fn ($name) => $name === $client);
            Factory::$application = $application;

            $this->assertSame(['onKunenaGetAccessControl' => 'onKunenaGetAccessControl'], Acl::getSubscribedEvents(), $client);
        }
    }
}
