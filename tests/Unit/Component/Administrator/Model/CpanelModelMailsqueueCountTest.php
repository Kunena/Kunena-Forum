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

namespace Kunena\Tests\Unit\Component\Administrator\Model;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseQuery;
use Kunena\Forum\Administrator\Model\CpanelModel;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The dashboard says how many e-mails wait to be sent: it counted every row of the mail queue, also the ones which were
 * sent (send = 1), so it showed 19 where the mail queue had 3 e-mails left.
 */
class CpanelModelMailsqueueCountTest extends UnitTestCase
{
    public function testOnlyTheMailsWhichAreNotSentAreCounted(): void
    {
        $this->requireJoomla();

        $where = [];
        $query = $this->createStub(DatabaseQuery::class);

        foreach (['select', 'from'] as $method) {
            $query->method($method)->willReturnSelf();
        }

        $query->method('where')->willReturnCallback(
            function ($condition) use (&$where, $query) {
                $where[] = implode(' AND ', (array) $condition);

                return $query;
            }
        );

        $db = $this->createStub(DatabaseDriver::class);
        $db->method('createQuery')->willReturn($query);
        $db->method('quoteName')->willReturnCallback(static fn ($name) => '`' . str_replace('.', '`.`', $name) . '`');
        $db->method('setQuery')->willReturnSelf();
        $db->method('loadResult')->willReturn('3');

        $model = $this->bare(CpanelModel::class);
        $model->setDatabase($db);

        $this->assertSame(3, $model->numberOfMailsqueues());
        $this->assertSame(['`a`.`send` = 0'], $where);
    }
}
