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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Topic;

use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The lookup of the option "Unique topic subjects", run on the real table of the topics.
 */
class KunenaTopicHelperSubjectExistsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const OWN_TOPICS_ONLY = '(a.category_id NOT IN (2) OR a.first_post_userid = 42)';

    private function topic(string $subject = 'I need help', int $hold = 0, int $movedId = 0, int $firstUser = 7): void
    {
        $this->insertRow('#__kunena_topics', [
            'id'                => 1,
            'category_id'       => 2,
            'subject'           => $subject,
            'hold'              => $hold,
            'moved_id'          => $movedId,
            'first_post_userid' => $firstUser,
        ]);
    }

    private function exists(string $subject, array $categoryIds = [2], string $ownTopicsWhere = ''): bool
    {
        return KunenaTopicHelper::subjectExists($this->getDBDriver(), $subject, $categoryIds, $ownTopicsWhere);
    }

    public function testTheSameSubjectMatchesWhateverTheCaseAndTheSpacesAtTheEnds(): void
    {
        $this->topic();

        $this->assertTrue($this->exists('I need help'));
        $this->assertTrue($this->exists('i NEED help'));
        $this->assertTrue($this->exists('  I need help  '));
    }

    public function testALongerSubjectDoesNotMatch(): void
    {
        $this->topic();

        $this->assertFalse($this->exists('I need help please'));
    }

    public function testATopicOutsideTheCategoriesDoesNotMatch(): void
    {
        $this->topic();

        $this->assertFalse($this->exists('I need help', [3]));
        $this->assertFalse($this->exists('I need help', []));
    }

    /**
     * @return  array<string, int[]>
     */
    public static function heldStates(): array
    {
        return [
            'unapproved'    => [1],
            'deleted'       => [2],
            'topic deleted' => [3],
        ];
    }

    #[DataProvider('heldStates')]
    public function testATopicWhichIsNotPublishedDoesNotMatch(int $hold): void
    {
        $this->topic('I need help', $hold);

        $this->assertFalse($this->exists('I need help'));
    }

    public function testTheShadowOfAMovedTopicDoesNotMatch(): void
    {
        $this->topic('I need help', 0, 5);

        $this->assertFalse($this->exists('I need help'));
    }

    public function testAccentsMakeAnotherSubject(): void
    {
        $this->topic('Café');

        $this->assertFalse($this->exists('Cafe'));
        $this->assertTrue($this->exists('café'));
    }

    public function testATopicOfAnotherUserInACategoryOfOwnTopicsOnlyDoesNotMatch(): void
    {
        $this->topic('I need help', 0, 0, 7);

        $this->assertFalse($this->exists('I need help', [2], self::OWN_TOPICS_ONLY));
    }

    public function testAnOwnTopicInACategoryOfOwnTopicsOnlyMatches(): void
    {
        $this->topic('I need help', 0, 0, 42);

        $this->assertTrue($this->exists('I need help', [2], self::OWN_TOPICS_ONLY));
    }

    public function testAnEmptySubjectNeverMatches(): void
    {
        $this->topic('');

        $this->assertFalse($this->exists(''));
        $this->assertFalse($this->exists('   '));
    }
}
