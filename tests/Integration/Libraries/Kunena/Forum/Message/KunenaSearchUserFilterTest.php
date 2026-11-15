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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Message;

use Kunena\Forum\Libraries\Forum\Message\KunenaSearchUserFilter;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The search "messages of this user" on the real tables (Kunena 7.1): the messages of a member by his account (also after a rename,
 * and by his nickname), the messages of a guest by the typed name, but not those of a guest who signed with the name of a member.
 */
class KunenaSearchUserFilterTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const NICKNAME_FIELD = 9;

    /**
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena', 'joomla'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->insertRow('#__users', ['id' => 42, 'name' => 'Jelle Kok', 'username' => 'jelle', 'params' => '', 'registerDate' => '2020-01-01 00:00:00']);
        $this->insertRow('#__users', ['id' => 43, 'name' => 'Anna', 'username' => 'anna', 'params' => '', 'registerDate' => '2020-01-01 00:00:00']);

        // 1: a post of Jelle, stored with his former name. 2: a guest who signed as "Jelle Kok". 3: a guest who signed as "Jelle the
        // guest" (a name of nobody). 4: a post of Anna.
        foreach ([[1, 42, 'Old name'], [2, 0, 'Jelle Kok'], [3, 0, 'Jelle the guest'], [4, 43, 'Anna']] as [$id, $userid, $name]) {
            $this->insertRow('#__kunena_messages', [
                'id' => $id, 'parent' => 0, 'thread' => 1, 'catid' => 2, 'userid' => $userid, 'name' => $name, 'subject' => 'Topic', 'time' => 1000,
            ]);
        }
    }

    /**
     * The ids of the messages which the condition finds.
     */
    private function find(string $typed, bool $exact, int $field = 0): array
    {
        $db   = $this->getDBDriver();
        $like = $db->quote($exact ? $typed : '%' . $typed . '%');

        $ids = $db->setQuery('SELECT m.id FROM #__kunena_messages AS m WHERE ' . KunenaSearchUserFilter::condition($like, $field) . ' ORDER BY m.id')
            ->loadColumn();

        return array_map('intval', $ids);
    }

    public function testAMemberIsFoundByHisAccountAndNotByAGuestWhoTookHisName(): void
    {
        // Message 1 (stored with another name) is his; message 2 (a guest who signed "Jelle Kok") is not.
        $this->assertSame([1], $this->find('Jelle Kok', true));
        $this->assertSame([1], $this->find('jelle', true));
    }

    public function testAGuestIsFoundByTheNameHeTyped(): void
    {
        $this->assertSame([3], $this->find('Jelle the guest', true));
    }

    public function testAPartOfTheName(): void
    {
        // His messages and those of the guest with a name of nobody; never the guest who signed as him.
        $this->assertSame([1, 3], $this->find('Jelle', false));
    }

    public function testAMemberIsFoundByHisNickname(): void
    {
        if (!\in_array($this->prefixed('#__fields_values'), $this->getDBDriver()->getTableList(), true)) {
            $this->markTestSkipped('The table of the custom fields is not in the schema of the tests.');
        }

        $this->insertRow('#__fields_values', ['field_id' => self::NICKNAME_FIELD, 'item_id' => '43', 'value' => 'Annie']);

        $this->assertSame([4], $this->find('Annie', true, self::NICKNAME_FIELD));
        $this->assertSame([], $this->find('Annie', true));
    }

    private function prefixed(string $table): string
    {
        return str_replace('#__', $this->getDBDriver()->getPrefix(), $table);
    }
}
