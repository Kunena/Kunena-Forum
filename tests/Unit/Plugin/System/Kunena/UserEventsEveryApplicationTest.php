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

namespace Kunena\Tests\Unit\Plugin\System\Kunena;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Kunena\Forum\Plugin\System\Kunena\Extension\Kunena;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The system plugin makes the profile of a new user and removes the data of a deleted user in every application. It listened
 * only in the site and the backend: a user deleted through the web services of Joomla (the API) kept his profile, bans, follows,
 * inbox and avatar files.
 */
class UserEventsEveryApplicationTest extends UnitTestCase
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

    private function eventsOf(string $client): array
    {
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('isClient')->willReturnCallback(static fn ($name) => $name === $client);
        Factory::$application = $application;

        return Kunena::getSubscribedEvents();
    }

    public function testTheApiSavesAndDeletesTheForumDataOfAUser(): void
    {
        $events = $this->eventsOf('api');

        $this->assertSame('onUserAfterDelete', $events['onUserAfterDelete'] ?? null);
        $this->assertSame('onUserAfterSave', $events['onUserAfterSave'] ?? null);

        // The events of the pages stay with their applications.
        $this->assertArrayNotHasKey('onUserBeforeSave', $events);
        $this->assertArrayNotHasKey('onAfterRender', $events);
        $this->assertArrayHasKey('onUserBeforeSave', $this->eventsOf('site'));
        $this->assertArrayHasKey('onAfterRender', $this->eventsOf('administrator'));
    }
}
