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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Topic;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Access\KunenaAccess;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Exception\KunenaExceptionAuthorise;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The author of a topic which waits for approval (hold 1) can read it (issue #5381); the others keep the rule of the holds.
 */
class KunenaTopicReadUnapprovedTest extends UnitTestCase
{
    private const int CATEGORY = 5;

    private const int AUTHOR = 42;

    private const int MODERATOR = 7;

    /**
     * The objects which the stand-ins replace, put back after the test.
     */
    private array $saved = [];

    protected function setUp(): void
    {
        // The classes of the topic and of the user need the framework of Joomla.
        $this->requireJoomla();

        // The category of the topic, the access with one moderator of that category, the configuration, a site application and
        // a language which gives back the keys.
        $category = self::bare(KunenaCategory::class, ['id' => self::CATEGORY, '_exists' => true, 'params' => new Registry()]);
        $access   = self::bare(KunenaAccess::class, ['adminsByUserid' => [], 'moderatorsByUserid' => [self::MODERATOR => [self::CATEGORY => 1]]]);
        $config   = self::bare(KunenaConfig::class, ['config' => ['modSeeDeleted' => '0']]);
        $site     = $this->createStub(CMSApplicationInterface::class);
        $site->method('isClient')->willReturn(false);
        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnArgument(0);

        $this->saved = [
            'categories'  => KunenaCategoryHelper::$_instances,
            'access'      => $this->replace(KunenaAccess::class, $access),
            'config'      => $this->replace(KunenaConfig::class, $config),
            'application' => Factory::$application,
            'language'    => Factory::$language,
        ];

        KunenaCategoryHelper::$_instances = [self::CATEGORY => $category];
        Factory::$application             = $site;
        Factory::$language                = $language;
    }

    protected function tearDown(): void
    {
        if (!$this->saved) {
            return;
        }

        KunenaCategoryHelper::$_instances = $this->saved['categories'];
        $this->replace(KunenaAccess::class, $this->saved['access']);
        $this->replace(KunenaConfig::class, $this->saved['config']);
        Factory::$application = $this->saved['application'];
        Factory::$language    = $this->saved['language'];
    }

    /**
     * Set the instance of a class with a static $instance and give back the one it had.
     */
    private function replace(string $class, mixed $instance): mixed
    {
        $property = new ReflectionProperty($class, 'instance');
        $old      = $property->getValue();
        $property->setValue(null, $instance);

        return $old;
    }

    private static function topic(int $hold, int $firstPostUserid = self::AUTHOR): KunenaTopic
    {
        return self::bare(KunenaTopic::class, [
            'id'                => 12,
            'category_id'       => self::CATEGORY,
            'hold'              => $hold,
            'first_post_userid' => $firstPostUserid,
            '_exists'           => true,
        ]);
    }

    private static function user(int $userid, bool $exists = true): KunenaUser
    {
        return self::bare(KunenaUser::class, ['userid' => $userid, '_exists' => $exists, 'banned' => null, 'blocked' => 0]);
    }

    /**
     * The error of the read rule, or null when the user can read the topic.
     */
    private function read(KunenaTopic $topic, KunenaUser $user): ?KunenaExceptionAuthorise
    {
        return $this->invoke($topic, 'authoriseRead', $user) ?: null;
    }

    public function testTheAuthorOfAnUnapprovedTopicCanReadIt(): void
    {
        $topic  = self::topic(1);
        $author = self::user(self::AUTHOR);

        $this->assertTrue($topic->isUnapprovedOf($author));
        $this->assertNull($this->read($topic, $author));
    }

    public function testAnotherUserCannotReadIt(): void
    {
        $topic = self::topic(1);
        $other = self::user(99);

        $this->assertFalse($topic->isUnapprovedOf($other));
        $this->assertSame(403, $this->read($topic, $other)?->getCode());
    }

    public function testAGuestCannotReadIt(): void
    {
        $topic = self::topic(1);
        $guest = self::user(0, false);

        $this->assertFalse($topic->isUnapprovedOf($guest));
        $this->assertSame(401, $this->read($topic, $guest)?->getCode());
    }

    public function testATopicStartedByAGuestIsNotReadableByAGuest(): void
    {
        $topic = self::topic(1, 0);
        $guest = self::user(0, false);

        $this->assertFalse($topic->isUnapprovedOf($guest));
        $this->assertSame(401, $this->read($topic, $guest)?->getCode());

        // Not either by a user object with the id 0 which says it exists.
        $this->assertFalse($topic->isUnapprovedOf(self::user(0)));
    }

    public function testTheAuthorOfADeletedTopicCannotReadIt(): void
    {
        $author = self::user(self::AUTHOR);

        foreach ([2, 3] as $hold) {
            $topic = self::topic($hold);

            $this->assertFalse($topic->isUnapprovedOf($author), "hold $hold");
            $this->assertSame(403, $this->read($topic, $author)?->getCode(), "hold $hold");
        }
    }

    public function testTheRuleDoesNotApplyToAnApprovedTopic(): void
    {
        $topic = self::topic(0);

        $this->assertFalse($topic->isUnapprovedOf(self::user(self::AUTHOR)));
        $this->assertNull($this->read($topic, self::user(self::AUTHOR)));
        $this->assertNull($this->read($topic, self::user(99)));
    }

    public function testAModeratorOfTheCategoryStillReadsAnUnapprovedTopicHeDidNotStart(): void
    {
        $topic     = self::topic(1);
        $moderator = self::user(self::MODERATOR);

        $this->assertFalse($topic->isUnapprovedOf($moderator));
        $this->assertNull($this->read($topic, $moderator));
    }
}
