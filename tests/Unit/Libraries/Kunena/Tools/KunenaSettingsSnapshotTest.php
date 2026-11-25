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

use Kunena\Forum\Libraries\Tools\KunenaSettingsSnapshot as S;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The tool "Settings snapshot" shows the settings which differ from the defaults and moves them to another site; the keys,
 * passwords and the e-mail address of the forum never leave their site.
 */
class KunenaSettingsSnapshotTest extends UnitTestCase
{
    public function testTheSettingsWhichDifferFromTheDefaults(): void
    {
        $defaults = ['boardTitle' => 'Kunena', 'pubWrite' => '0', 'maxLinks' => '6', 'ebayApiKey' => ''];
        $current  = ['boardTitle' => 'My forum', 'pubWrite' => false, 'maxLinks' => 6, 'ebayApiKey' => 'SECRET'];

        $this->assertSame(['boardTitle' => ['value' => 'My forum', 'default' => 'Kunena']], S::changed($current, $defaults));
    }

    public function testTheSettingsWhichStayOnTheirSite(): void
    {
        foreach (['googleMapApiKey', 'ebayApiKey', 'blueskyappPasswordOfApp', 'XConsumerKey', 'XConsumerSecret', 'stopForumSpamKey', 'email'] as $name) {
            $this->assertTrue(S::isPrivate($name), $name);
        }

        $this->assertFalse(S::isPrivate('boardTitle'));
    }

    public function testTheFileHasNoSecret(): void
    {
        $json = S::export(['boardTitle' => 'My forum', 'stopForumSpamKey' => 'SECRET', 'email' => 'me@example.org'], '8.0.0', '2026-11-25 10:00:00');

        $this->assertStringNotContainsString('SECRET', $json);
        $this->assertStringNotContainsString('me@example.org', $json);
        $this->assertSame(['boardTitle' => 'My forum'], S::read($json));
    }

    public function testAFileWhichIsNoSnapshot(): void
    {
        $this->assertNull(S::read('{"settings": {"a": 1}}'));
        $this->assertNull(S::read('not json'));
        $this->assertNull(S::read(str_repeat(' ', S::MAX_FILE + 1)));
        // A list or an object is no value of a setting.
        $this->assertSame(['a' => 1], S::read('{"kunena": "8.0", "settings": {"a": 1, "b": [1], "c": {"x": 1}}}'));
    }

    public function testThePreviewOfAnImport(): void
    {
        $current = ['boardTitle' => 'Old', 'pubWrite' => '0', 'stopForumSpamKey' => 'MINE'];
        $preview = S::preview(['boardTitle' => 'New', 'pubWrite' => 0, 'stopForumSpamKey' => 'THEIRS', 'unknownOption' => 1], $current);

        $this->assertSame(['boardTitle' => ['from' => 'Old', 'to' => 'New']], $preview['changes']);
        $this->assertSame(['stopForumSpamKey', 'unknownOption'], $preview['ignored']);
    }
}
