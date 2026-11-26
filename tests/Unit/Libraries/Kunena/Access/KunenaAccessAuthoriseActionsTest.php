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

namespace Kunena\Tests\Unit\Libraries\Kunena\Access;

use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The actions which the access plugins allow or refuse in a category ("TODO: allow plugin to customise the error"): a plugin
 * refuses with false, or with its own reason (a KunenaExceptionAuthorise), which the category gives to the user. A plugin which
 * allows the action wins.
 */
class KunenaAccessAuthoriseActionsTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();
    }

    /**
     * The actions of the plugins of the access type of a category, each plugin giving its list.
     */
    private static function actions(array ...$lists): array
    {
        $plugins = array_map(
            static fn (array $list) => new class ($list) {
                public function __construct(private array $list)
                {
                }

                public function getAuthoriseActions(KunenaCategory $category, int $userid): array
                {
                    return $this->list;
                }
            },
            $lists
        );

        $access   = self::bare(KunenaAccess::class, ['accesstypes' => ['all' => [], 'joomla.level' => $plugins]]);
        $category = self::bare(KunenaCategory::class, ['accesstype' => 'joomla.level']);

        return $access->authoriseActions($category, 42);
    }

    public function testAPluginRefusesWithFalse(): void
    {
        $this->assertSame(['topic.create' => false], self::actions(['topic.create' => false]));
    }

    public function testAPluginRefusesWithItsReason(): void
    {
        $reason  = new KunenaExceptionAuthorise('Only the members of the club may start a topic here.', 403);
        $actions = self::actions(['topic.create' => $reason]);

        $this->assertSame($reason, $actions['topic.create']);
    }

    public function testAPluginWhichAllowsWins(): void
    {
        $reason = new KunenaExceptionAuthorise('No.', 403);

        $this->assertSame(['topic.create' => true], self::actions(['topic.create' => $reason], ['topic.create' => true]));
        $this->assertSame(['topic.create' => true], self::actions(['topic.create' => true], ['topic.create' => $reason]));
        $this->assertSame(['topic.create' => true], self::actions(['topic.create' => false], ['topic.create' => true]));
    }

    public function testAReasonIsKeptOverARefusalWithoutOne(): void
    {
        $reason = new KunenaExceptionAuthorise('No.', 403);

        $this->assertSame($reason, self::actions(['topic.create' => false], ['topic.create' => $reason])['topic.create']);
        $this->assertSame($reason, self::actions(['topic.create' => $reason], ['topic.create' => false])['topic.create']);
    }

    public function testACategoryWithoutPluginsHasNoActions(): void
    {
        $access = self::bare(KunenaAccess::class, ['accesstypes' => ['all' => []]]);

        $this->assertSame([], $access->authoriseActions(self::bare(KunenaCategory::class, ['accesstype' => 'none']), 42));
    }
}
