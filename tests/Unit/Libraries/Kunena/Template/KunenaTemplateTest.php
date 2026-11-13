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

namespace Kunena\Tests\Unit\Libraries\Kunena\Template;

use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The system icon of a topic (without selectable topic icons): a shadow topic shows the moved icon, also while it waits for
 * approval, and a deleted topic shows the deleted icon, also when it is a shadow.
 */
class KunenaTemplateTest extends UnitTestCase
{
    public static function topics(): array
    {
        return [
            'shadow, unapproved'         => [['moved_id' => 10, 'hold' => 1, 'posts' => 1], 5],
            'unapproved'                 => [['moved_id' => 0, 'hold' => 1, 'posts' => 1], 1],
            'shadow, deleted'            => [['moved_id' => 10, 'hold' => 2, 'posts' => 1], 2],
            'shadow, deleted with topic' => [['moved_id' => 10, 'hold' => 3, 'posts' => 1], 2],
            'shadow, approved'           => [['moved_id' => 10, 'hold' => 0, 'posts' => 1], 5],
            'unanswered'                 => [['posts' => 1], 6],
            'normal'                     => [['posts' => 5], 0],
            'sticky'                     => [['posts' => 5, 'ordering' => 1], 3],
            'locked'                     => [['posts' => 5, 'locked' => 1], 4],
            'sticky and locked'          => [['posts' => 5, 'ordering' => 1, 'locked' => 1], 7],
            'unapproved sticky'          => [['posts' => 5, 'ordering' => 1, 'hold' => 1], 1],
        ];
    }

    #[DataProvider('topics')]
    public function testTheSystemIconIdOfATopic(array $properties, int $iconid): void
    {
        $this->requireJoomla();

        $template = self::bare(KunenaTemplate::class);
        $topic    = self::bare(KunenaTopic::class, $properties + ['moved_id' => 0, 'hold' => 0, 'ordering' => 0, 'locked' => 0]);

        $this->assertSame($iconid, self::invoke($template, 'getSystemTopicIconId', $topic));
    }
}
