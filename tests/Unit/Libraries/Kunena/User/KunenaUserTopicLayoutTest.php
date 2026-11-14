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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Joomla\CMS\Application\CMSApplication;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The topic layout (flat, threaded or indented) which the topic page shows: the one asked for, else the choice of the user in this
 * session, else the choice saved in the profile, else the default layout of the configuration.
 */
class KunenaUserTopicLayoutTest extends UnitTestCase
{
    private function user(?string $session, mixed $view, string $config = 'flat'): KunenaUser
    {
        $this->requireJoomla();

        $app = $this->createStub(CMSApplication::class);
        $app->method('getUserState')->willReturn($session);

        return self::bare(KunenaUser::class, [
            'view'    => $view,
            '_config' => self::bare(KunenaConfig::class, ['config' => ['topicLayout' => $config]]),
            '_app'    => $app,
        ]);
    }

    public static function layouts(): array
    {
        return [
            'flat'     => ['flat'],
            'threaded' => ['threaded'],
            'indented' => ['indented'],
        ];
    }

    #[DataProvider('layouts')]
    public function testTheLayoutAskedForWins(string $layout): void
    {
        $this->assertSame($layout, $this->user('threaded', 'indented', 'threaded')->getTopicLayout($layout));
    }

    #[DataProvider('layouts')]
    public function testWithoutALayoutTheChoiceOfTheSessionIsUsed(string $layout): void
    {
        $user = $this->user($layout, 'flat');

        $this->assertSame($layout, $user->getTopicLayout());
        $this->assertSame($layout, $user->getTopicLayout('default'));
    }

    #[DataProvider('layouts')]
    public function testWithoutAChoiceInTheSessionTheProfileIsUsed(string $layout): void
    {
        $this->assertSame($layout, $this->user(null, $layout, 'flat')->getTopicLayout());
    }

    #[DataProvider('layouts')]
    public function testWithoutAnyChoiceTheConfigurationIsUsed(string $layout): void
    {
        $this->assertSame($layout, $this->user(null, 0, $layout)->getTopicLayout());
    }

    public function testAnUnknownLayoutGivesTheLayoutOfTheConfiguration(): void
    {
        $this->assertSame('indented', $this->user('threaded', 'flat', 'indented')->getTopicLayout('tree'));
        $this->assertSame('threaded', $this->user('default', 'flat', 'threaded')->getTopicLayout());
    }
}
