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

use Kunena\Forum\Libraries\Forum\Message\KunenaMessageHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * The registered users who posted from an IP address (the marker next to the IP address in the topic), on the real tables of
 * the messages and of the users.
 */
class KunenaMessageHelperUsersByIpTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private int $nextId = 1;

    /**
     * The users 10, 11, 12 and 13 exist.
     *
     * @return  void
     */
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([10, 11, 12, 13] as $id) {
            $this->insertRow('#__users', ['id' => $id, 'name' => 'User ' . $id, 'username' => 'user' . $id, 'params' => '']);
        }
    }

    /**
     * @return  string[]
     */
    public function getSchemasToLoad(): array
    {
        return ['kunena', 'joomla'];
    }

    private function messages(int $userid, string $ip, int $count = 1, int $hold = 0): void
    {
        for ($i = 0; $i < $count; $i++) {
            $this->insertRow('#__kunena_messages', ['id' => $this->nextId++, 'thread' => 1, 'catid' => 2, 'userid' => $userid, 'ip' => $ip, 'hold' => $hold]);
        }
    }

    private function lookup(array $ips): array
    {
        KunenaMessageHelper::cleanup();

        return KunenaMessageHelper::getUsersByIp($this->getDBDriver(), $ips);
    }

    public function testTheUsersOfAnIpAreCountedMostMessagesFirstThenByUserId(): void
    {
        $this->messages(11, '10.0.0.1', 1);
        $this->messages(10, '10.0.0.1', 3, 2);
        $this->messages(13, '10.0.0.1', 1, 1);
        $this->messages(12, '10.0.0.1', 3);
        $this->messages(12, '10.0.0.2', 4);

        $result = $this->lookup(['10.0.0.1']);

        $this->assertSame(['10.0.0.1' => [10 => 3, 12 => 3, 11 => 1, 13 => 1]], $result);
    }

    public function testSeveralIpsAreLookedUpInOneCall(): void
    {
        $this->messages(10, '10.0.0.1', 2);
        $this->messages(11, '10.0.0.2', 1);
        $this->messages(10, '10.0.0.2', 1);

        $result = $this->lookup(['10.0.0.1', '10.0.0.2', '10.0.0.1']);

        $this->assertSame(['10.0.0.1' => [10 => 2], '10.0.0.2' => [10 => 1, 11 => 1]], $result);
    }

    public function testAnIpWithoutMessagesGivesNoUsers(): void
    {
        $this->messages(10, '10.0.0.1');

        $this->assertSame(['10.0.0.9' => []], $this->lookup(['10.0.0.9']));
    }

    public function testGuestsAreNotCounted(): void
    {
        $this->messages(0, '10.0.0.1', 2);
        $this->messages(10, '10.0.0.1');

        $this->assertSame(['10.0.0.1' => [10 => 1]], $this->lookup(['10.0.0.1']));
    }

    public function testAUserWhoIsNotInTheUsersIsNotCounted(): void
    {
        $this->messages(99, '10.0.0.1', 2);
        $this->messages(10, '10.0.0.1');

        $this->assertSame(['10.0.0.1' => [10 => 1]], $this->lookup(['10.0.0.1']));
    }

    public function testEmptyIpsAreIgnored(): void
    {
        $this->messages(10, '');
        $this->messages(11, '10.0.0.1');

        $this->assertSame([], $this->lookup(['', '']));
        $this->assertSame(['10.0.0.1' => [11 => 1]], $this->lookup(['', '10.0.0.1']));
    }

    public function testTheResultIsCachedUntilTheCleanup(): void
    {
        $this->messages(10, '10.0.0.1');

        $this->assertSame(['10.0.0.1' => [10 => 1]], $this->lookup(['10.0.0.1']));

        $this->messages(11, '10.0.0.1');
        $this->messages(11, '10.0.0.2');

        $db = $this->getDBDriver();

        $this->assertSame(['10.0.0.1' => [10 => 1]], KunenaMessageHelper::getUsersByIp($db, ['10.0.0.1']));
        $this->assertSame(['10.0.0.2' => [11 => 1]], KunenaMessageHelper::getUsersByIp($db, ['10.0.0.2']));
        $this->assertSame(['10.0.0.1' => [10 => 1, 11 => 1]], $this->lookup(['10.0.0.1']));
    }
}
