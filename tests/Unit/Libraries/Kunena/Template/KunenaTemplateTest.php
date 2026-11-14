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
 * The icon and the label of a topic. The system icon (without selectable topic icons): a shadow topic shows the moved icon,
 * also while it waits for approval, and a deleted topic shows the deleted icon, also when it is a shadow. With selectable
 * topic icons a moved, deleted, sticky or locked topic shows its status icon without losing the icon chosen for it, and
 * the label follows the chosen icon or label: "Sticky" only for a sticky topic without one.
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

    public static function userIconTopics(): array
    {
        return [
            'normal, chosen icon' => [['icon_id' => 8], 8],
            'sticky'              => [['icon_id' => 3, 'ordering' => 1], 504],
            'locked'              => [['icon_id' => 1, 'locked' => 1], 505],
            'sticky and locked'   => [['icon_id' => 8, 'ordering' => 1, 'locked' => 1], 503],
            'deleted'             => [['icon_id' => 1, 'hold' => 2, 'ordering' => 1, 'locked' => 1], 501],
            'deleted with topic'  => [['icon_id' => 1, 'hold' => 3], 501],
            'moved'               => [['icon_id' => 2, 'moved_id' => 10, 'hold' => 2], 500],
            'moved and locked'    => [['icon_id' => 2, 'moved_id' => 10, 'locked' => 1], 500],
        ];
    }

    #[DataProvider('userIconTopics')]
    public function testTheIconIdOfATopicWithSelectableIcons(array $properties, int $iconid): void
    {
        $this->requireJoomla();

        $template = self::bare(KunenaTemplate::class);
        $topic    = self::bare(KunenaTopic::class, $properties + ['moved_id' => 0, 'hold' => 0, 'ordering' => 0, 'locked' => 0]);
        $chosen   = $topic->icon_id;

        $this->assertSame($iconid, self::invoke($template, 'getUserTopicIconId', $topic));
        $this->assertSame($chosen, $topic->icon_id);
    }

    public static function labelTopics(): array
    {
        return [
            'locked, icon 1'            => [['icon_id' => 1, 'locked' => 1], 1, 1],
            'sticky, icon 3'            => [['icon_id' => 3, 'ordering' => 1], 1, 3],
            'sticky, icon 0'            => [['icon_id' => 0, 'ordering' => 1], 1, 11],
            'sticky and locked, icon 8' => [['icon_id' => 8, 'ordering' => 1, 'locked' => 1], 1, 8],
            'sticky, label 4'           => [['icon_id' => 1, 'label_id' => 4, 'ordering' => 1], 2, 4],
            'sticky, label 0'           => [['icon_id' => 1, 'label_id' => 0, 'ordering' => 1], 2, 11],
        ];
    }

    #[DataProvider('labelTopics')]
    public function testTheLabelIdOfATopic(array $properties, int $topiclabels, int $labelid): void
    {
        $this->requireJoomla();

        $template = self::bare(KunenaTemplate::class);
        $topic    = self::bare(KunenaTopic::class, $properties + ['label_id' => 0, 'ordering' => 0, 'locked' => 0]);

        $this->assertSame($labelid, self::invoke($template, 'getTopicLabelId', $topic, $topiclabels));
    }
}
