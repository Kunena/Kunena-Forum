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

namespace Kunena\Forum\Tests\Unit\Email;

use Kunena\Forum\Tests\Unit\Support\Files;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The mail templates of Joomla which the update of 7.2 adds: every text of a template is in the language file, and every tag
 * which the texts use is declared for the template (Joomla shows the declared tags to the editor of the template).
 */
class MailTemplateRowsTest extends TestCase
{
    /**
     * @return  array<string,array{subject:string,body:string,htmlbody:string,tags:string[]}>
     */
    private static function rows(): array
    {
        $sql = Files::read('src/admin/sql/updates/mysql/7.2.0-DEV.sql');

        preg_match_all(
            "/INSERT IGNORE INTO `#__mail_templates`[^;]*?VALUES \('(com_kunena\.\w+)', 'com_kunena', '', '(\w+)', '(\w+)', '(\w+)', '', '(\{.*?\})'\);/s",
            $sql,
            $matches,
            PREG_SET_ORDER
        );

        $rows = [];

        foreach ($matches as $match) {
            $params = json_decode($match[5], true);

            $rows[$match[1]] = ['subject' => $match[2], 'body' => $match[3], 'htmlbody' => $match[4], 'tags' => $params['tags'] ?? []];
        }

        return $rows;
    }

    public static function templates(): array
    {
        return array_map(static fn ($id) => [$id], array_keys(self::rows()));
    }

    public function testTheFourTemplatesAreAdded(): void
    {
        $this->assertSame(
            ['com_kunena.subscription', 'com_kunena.moderator', 'com_kunena.mention', 'com_kunena.reportmessage'],
            array_keys(self::rows())
        );
    }

    #[DataProvider('templates')]
    public function testTheTagsAreAListOfNames(string $id): void
    {
        $tags = self::rows()[$id]['tags'];

        $this->assertNotEmpty($tags);

        foreach ($tags as $tag) {
            $this->assertMatchesRegularExpression('/^[a-z_]+$/', $tag);
        }

        $this->assertSame($tags, array_values(array_unique($tags)));
    }

    #[DataProvider('templates')]
    public function testEveryTextOfTheTemplateIsInTheLanguageFile(string $id): void
    {
        $strings = Files::language('src/site/language/en-GB/com_kunena.ini');

        foreach (['subject', 'body', 'htmlbody'] as $part) {
            $key = self::rows()[$id][$part];

            $this->assertArrayHasKey($key, $strings, "$id: the text $key is missing in the language file.");
            $this->assertNotSame('', $strings[$key]);
        }
    }

    #[DataProvider('templates')]
    public function testTheTextsUseOnlyDeclaredTags(string $id): void
    {
        $strings = Files::language('src/site/language/en-GB/com_kunena.ini');
        $tags    = array_map('strtoupper', self::rows()[$id]['tags']);

        foreach (['subject', 'body', 'htmlbody'] as $part) {
            $key = self::rows()[$id][$part];

            preg_match_all('/\{([A-Z_]+)\}/', $strings[$key], $used);

            foreach (array_unique($used[1]) as $tag) {
                $this->assertContains($tag, $tags, "$id: the text $key uses {{$tag}}, which the template does not declare.");
            }
        }
    }

    #[DataProvider('templates')]
    public function testTheLinkOfTheMailIsInTheBodies(string $id): void
    {
        $strings = Files::language('src/site/language/en-GB/com_kunena.ini');
        $row     = self::rows()[$id];

        $this->assertStringContainsString('{URL}', $strings[$row['body']]);
        $this->assertStringContainsString('{URL}', $strings[$row['htmlbody']]);
    }

    #[DataProvider('templates')]
    public function testTheHtmlBodyHasNoStylesheetAndNoScript(string $id): void
    {
        $html = Files::language('src/site/language/en-GB/com_kunena.ini')[self::rows()[$id]['htmlbody']];

        // The mail programs ignore stylesheets: the styles are on the elements. A script never belongs in a mail.
        $this->assertStringNotContainsString('<style', $html);
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringContainsString("style='", $html);
    }

    public function testTheSubscriptionAndTheModeratorShareTheirTexts(): void
    {
        $rows = self::rows();

        $this->assertSame($rows['com_kunena.subscription']['subject'], $rows['com_kunena.moderator']['subject']);
        $this->assertSame($rows['com_kunena.subscription']['htmlbody'], $rows['com_kunena.moderator']['htmlbody']);
    }

    public function testTheTemplatesAreRemovedWhenKunenaIsRemoved(): void
    {
        $install = Files::read('src/libraries/kunena/src/Install/KunenaModelInstall.php');

        $this->assertStringContainsString("quoteName('extension') . ' = ' . \$this->db->quote('com_kunena')", $install);
    }
}
