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

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The copies of the first and the last post in a topic are as long as a post (mediumtext, issue #9719), in every way a site gets
 * its tables.
 */
class KunenaTopicPostMessageColumnsTest extends UnitTestCase
{
    private const string MODIFY = 'ALTER TABLE `#__kunena_topics` MODIFY `first_post_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL, MODIFY `last_post_message` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL';

    public function testANewInstallCreatesMediumtextColumns(): void
    {
        $sql = Files::read('src/admin/sql/install/mysql/create_tables.utf8.sql');

        $this->assertSame(1, preg_match('/CREATE TABLE IF NOT EXISTS `#__kunena_topics`[^;]*/i', $sql, $m));
        $this->assertMatchesRegularExpression('/`first_post_message`\s+mediumtext\s+NULL,/i', $m[0]);
        $this->assertMatchesRegularExpression('/`last_post_message`\s+mediumtext\s+NULL,/i', $m[0]);
    }

    public function testTheSchemaOfTheInstallerHasMediumtextColumns(): void
    {
        $xml = simplexml_load_file(Files::path('src/admin/sql/install/schema/install.xml'));

        foreach (['kunena_topics' => ['first_post_message', 'last_post_message'], 'kunena_messages_text' => ['message']] as $table => $names) {
            foreach ($names as $name) {
                $fields = $xml->xpath('//table[@name="' . $table . '"]/field[@name="' . $name . '"]');

                $this->assertCount(1, $fields, "The schema of the installer has no $name on $table.");
                $this->assertSame('mediumtext', (string) $fields[0]['type'], "$table.$name");
            }
        }
    }

    public function testTheUpgradeFileOfTheInstallerWidensTheColumns(): void
    {
        $xml     = simplexml_load_file(Files::path('src/admin/install/kunena.install.upgrade.xml'));
        $queries = [];

        foreach ($xml->upgrade[0]->version as $version) {
            if ((string) $version['version'] === '8.0.0-DEV') {
                foreach ($version->query as $query) {
                    $queries[] = trim((string) $query);
                }
            }
        }

        $this->assertContains(self::MODIFY, $queries, 'The block 8.0.0-DEV does not widen the copies of the posts.');
    }

    public function testTheUpdateFileOfJoomlaWidensTheColumns(): void
    {
        $this->assertStringContainsString(self::MODIFY . ';', Files::read('src/admin/sql/updates/mysql/7.2.0-DEV.sql'));
    }
}
