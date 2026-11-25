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

use Kunena\Forum\Libraries\Tools\KunenaUserMerge as M;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Merge users" moves every column which names a user, except the logs and the history of the bans; a column in a
 * key with another one is moved without making two rows for the same item.
 */
class KunenaUserMergeTest extends UnitTestCase
{
    public function testTwoDifferentUsersAreNeeded(): void
    {
        $this->assertSame('COM_KUNENA_TOOLS_MERGE_ERROR_CHOOSE', M::check(0, 5));
        $this->assertSame('COM_KUNENA_TOOLS_MERGE_ERROR_CHOOSE', M::check(5, 0));
        $this->assertSame('COM_KUNENA_TOOLS_MERGE_ERROR_SAME', M::check(5, 5));
        $this->assertNull(M::check(5, 6));
    }

    /**
     * Every column of the schema which names a user is moved by the merge, or is kept on purpose.
     */
    public function testEveryUserColumnOfTheSchemaIsHandled(): void
    {
        $schema = Files::read('src/admin/sql/install/mysql/create_tables.utf8.sql');
        $kept   = [
            '#__kunena_logs'         => ['user_id', 'target_user'],
            '#__kunena_users_banned' => ['userid', 'created_by', 'modified_by'],
            '#__kunena_users'        => ['userid'],
            '#__kunena_sessions'     => ['userid'],
        ];

        $handled = [];

        foreach (M::allColumns() as [$table, $column]) {
            $handled[$table][] = $column;
        }

        preg_match_all('#CREATE TABLE IF NOT EXISTS `(\#__kunena_[a-z_]+)`\s*\((.*?)\)\s*ENGINE#s', $schema, $tables, PREG_SET_ORDER);

        $this->assertNotEmpty($tables);

        foreach ($tables as [, $table, $body]) {
            // The columns of a user id are in lower case (userListtime is a setting of the user, not a user).
            preg_match_all('#^\s+`?([a-z_]*(?:user|author|_by|followed|ignored)[a-z_]*)`?\s+int#m', $body, $columns);

            foreach ($columns[1] as $column) {
                $this->assertTrue(
                    \in_array($column, $handled[$table] ?? [], true) || \in_array($column, $kept[$table] ?? [], true),
                    "$table.$column is not moved by the merge"
                );
            }
        }
    }

    public function testTheRowsOfOneUserWithHimselfAreInTablesWhichTheMergeChanges(): void
    {
        $columns = M::allColumns();

        foreach (M::SELF_ROWS as [$table, $first, $second]) {
            $this->assertTrue(\in_array([$table, $first], $columns, true) || \in_array([$table, $second], $columns, true), $table);
        }
    }
}
