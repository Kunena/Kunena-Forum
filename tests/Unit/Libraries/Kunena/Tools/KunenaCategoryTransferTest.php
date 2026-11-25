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

namespace Kunena\Tests\Unit\Libraries\Kunena\Tools;

use Kunena\Forum\Libraries\Tools\KunenaCategoryTransfer as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Export and import" writes a branch of the categories (no topic, no counter) and the configuration without its
 * secrets, and reads it back in an order where each parent comes before its children.
 */
class KunenaCategoryTransferTest extends UnitTestCase
{
    private static function rows(): array
    {
        return [
            ['id' => 1, 'parentid' => 0, 'name' => 'Section', 'ordering' => 1, 'numTopics' => 5, 'numPosts' => 9, 'last_post_id' => 3, 'channels' => 'THIS'],
            ['id' => 2, 'parentid' => 1, 'name' => 'B', 'ordering' => 2, 'numTopics' => 1, 'numPosts' => 1, 'last_post_id' => 4, 'channels' => '1,3'],
            ['id' => 3, 'parentid' => 1, 'name' => 'A', 'ordering' => 1, 'numTopics' => 1, 'numPosts' => 1, 'last_post_id' => 5, 'channels' => 'THIS'],
            ['id' => 4, 'parentid' => 3, 'name' => 'A child', 'ordering' => 1, 'numTopics' => 0, 'numPosts' => 0, 'last_post_id' => 0, 'channels' => 'THIS'],
            ['id' => 5, 'parentid' => 0, 'name' => 'Other', 'ordering' => 2, 'numTopics' => 0, 'numPosts' => 0, 'last_post_id' => 0, 'channels' => 'THIS'],
        ];
    }

    public function testBranchIsTheCategoryAndAllBelowIt(): void
    {
        $this->assertSame([3, 4], T::branch(self::rows(), 3));
        $this->assertEqualsCanonicalizing([1, 2, 3, 4], T::branch(self::rows(), 1));
        $this->assertSame([1, 2, 3, 4, 5], T::branch(self::rows(), 0));
        $this->assertSame([], T::branch(self::rows(), 99));
    }

    public function testSortTreePutsEachParentBeforeItsChildrenInTheirOrder(): void
    {
        $this->assertSame([1, 5, 3, 2, 4], array_column(T::sortTree(self::rows()), 'id'));
    }

    public function testACategoryWithoutItsParentBecomesATopOne(): void
    {
        $sorted = T::sortTree([['id' => 4, 'parentid' => 3, 'ordering' => 1], ['id' => 7, 'parentid' => 7, 'ordering' => 1]]);

        $this->assertSame([0, 0], array_column($sorted, 'parentid'));
    }

    public function testExportKeepsNoCounterAndTheBranchStartsAtTheTop(): void
    {
        $data = T::export(self::rows(), 3, null);

        $this->assertSame(T::FORMAT, $data['format']);
        $this->assertSame([3, 4], array_column($data['categories'], 'id'));
        $this->assertSame(0, $data['categories'][0]['parentid']);
        $this->assertArrayNotHasKey('numTopics', $data['categories'][0]);
        $this->assertArrayNotHasKey('last_post_id', $data['categories'][0]);
        $this->assertArrayNotHasKey('config', $data);
    }

    public function testTheConfigurationLeavesWithoutItsSecrets(): void
    {
        $data = T::export(self::rows(), 0, ['boardTitle' => 'Forum', 'stopForumSpamKey' => 'secret', 'XConsumerSecret' => 'secret', 'list' => [1, 2]]);

        $this->assertSame(['boardTitle' => 'Forum'], $data['config']);
    }

    public function testValidateRefusesWhatIsNoExport(): void
    {
        $this->assertSame('COM_KUNENA_TOOLS_TRANSFER_ERROR_FORMAT', T::validate(null));
        $this->assertSame('COM_KUNENA_TOOLS_TRANSFER_ERROR_FORMAT', T::validate(['format' => 'other']));
        $this->assertSame('COM_KUNENA_TOOLS_TRANSFER_ERROR_VERSION', T::validate(['format' => T::FORMAT, 'version' => T::VERSION + 1]));
        $this->assertSame('COM_KUNENA_TOOLS_TRANSFER_ERROR_CATEGORY', T::validate(['format' => T::FORMAT, 'version' => 1, 'categories' => [['name' => 'x']]]));
        $this->assertSame('COM_KUNENA_TOOLS_TRANSFER_ERROR_FORMAT', T::validate(['format' => T::FORMAT, 'version' => 1, 'config' => 'x']));
        $this->assertNull(T::validate(T::export(self::rows(), 0, ['boardTitle' => 'Forum'])));
    }

    public function testAnImportChangesOnlyTheKnownOptionsAndNoSecret(): void
    {
        $changes = T::configChanges(
            ['boardTitle' => 'Old', 'threadsPerPage' => 20, 'stopForumSpamKey' => 'mine'],
            ['boardTitle' => 'New', 'threadsPerPage' => '20', 'stopForumSpamKey' => 'theirs', 'unknown' => 1, 'boardOffline' => ['x']]
        );

        $this->assertSame(['boardTitle' => 'New'], $changes);
    }

    public function testTheChannelsGetTheNewIds(): void
    {
        $this->assertSame('11,13', T::mapChannels('1,3', [1 => 11, 3 => 13]));
        $this->assertSame('THIS,13', T::mapChannels('THIS, 3, 99', [3 => 13]));
        $this->assertSame('THIS', T::mapChannels('99', []));
        $this->assertSame('THIS', T::mapChannels('', []));
    }
}
