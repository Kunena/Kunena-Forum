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
 * The topic layout is always flat: the threaded and indented layouts are no longer offered.
 */
class KunenaUserTopicLayoutTest extends UnitTestCase
{
    public static function layouts(): array
    {
        return [
            'none'     => [null],
            'default'  => ['default'],
            'flat'     => ['flat'],
            'threaded' => ['threaded'],
            'indented' => ['indented'],
            'unknown'  => ['tree'],
        ];
    }

    #[DataProvider('layouts')]
    public function testTheLayoutIsFlatWhateverTheUserTheSessionAndTheConfigurationAsk(?string $layout): void
    {
        $this->requireJoomla();

        $app = $this->createStub(CMSApplication::class);
        $app->method('getUserState')->willReturn('threaded');

        $user = self::bare(KunenaUser::class, [
            'view'    => 'threaded',
            '_config' => self::bare(KunenaConfig::class, ['config' => ['topicLayout' => 'indented']]),
            '_app'    => $app,
        ]);

        $this->assertSame('flat', $user->getTopicLayout($layout));
    }
}
