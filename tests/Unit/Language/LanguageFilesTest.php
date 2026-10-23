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

namespace Kunena\Tests\Unit\Language;

use Kunena\Tests\Unit\Support\Files;
use PHPUnit\Framework\Attributes\DataProvider;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The English language files: every line is valid (a wrong quote breaks the file for Joomla), and the texts of the features of
 * 7.2 are there.
 */
class LanguageFilesTest extends UnitTestCase
{
    public static function files(): array
    {
        $files = [];

        foreach (['src/admin/language/en-GB', 'src/site/language/en-GB', 'src/plugins/plg_kunena_geoip/language/en-GB'] as $folder) {
            foreach (glob(Files::path($folder) . '/*.ini') ?: [] as $file) {
                $files[$folder . '/' . basename($file)] = [$folder . '/' . basename($file)];
            }
        }

        return $files;
    }

    #[DataProvider('files')]
    public function testEveryLineIsAStringOrAComment(string $file): void
    {
        foreach (explode("\n", Files::read($file)) as $number => $line) {
            $line = trim($line);

            if ($line === '' || $line[0] === ';' || $line[0] === '[') {
                continue;
            }

            $this->assertMatchesRegularExpression(
                '/^[A-Za-z0-9_.]+\s*=\s*".*"$/',
                $line,
                $file . ' line ' . ($number + 1) . ' is not a string of a language file.'
            );

            // A double quote inside the text must be escaped (\") or written as "_QQ_".
            $text = str_replace('\\"', '', (string) preg_replace('/^[A-Za-z0-9_.]+\s*=\s*"(.*)"$/', '$1', $line));

            $this->assertStringNotContainsString('"', $text, $file . ' line ' . ($number + 1) . ' has a double quote inside the text.');
        }
    }

    public static function newTexts(): array
    {
        $site  = 'src/site/language/en-GB/com_kunena.ini';
        $admin = 'src/admin/language/en-GB/com_kunena.views.ini';

        return [
            'ban box title'            => [$site, 'COM_KUNENA_BAN_QUICK_TITLE'],
            'ban button'               => [$site, 'COM_KUNENA_BUTTON_MESSAGE_BAN'],
            'default announcement'     => [$site, 'COM_KUNENA_ANN_BANNER'],
            'default set message'      => [$site, 'COM_KUNENA_ANN_BANNER_SET'],
            'default not visible'      => [$site, 'COM_KUNENA_ANN_BANNER_NOT_VISIBLE'],
            'popup mention'            => [$site, 'COM_KUNENA_POPUP_MENTION'],
            'popup reply own'          => [$site, 'COM_KUNENA_POPUP_REPLY_OWN'],
            'popup reply subscribed'   => [$site, 'COM_KUNENA_POPUP_REPLY_SUBSCRIBED'],
            'popup new topic'          => [$site, 'COM_KUNENA_POPUP_NEW_TOPIC'],
            'popup open'               => [$site, 'COM_KUNENA_POPUP_OPEN'],
            'popup user setting'       => [$site, 'COM_KUNENA_USER_POPUP_NOTIFICATIONS'],
            'mail subject'             => [$site, 'COM_KUNENA_MAILTPL_NOTIFICATION_SUBJECT'],
            'mail mention body'        => [$site, 'COM_KUNENA_MAILTPL_MENTION_BODY'],
            'mail report html'         => [$site, 'COM_KUNENA_MAILTPL_REPORT_HTMLBODY'],
            'mail templates option'    => [$admin, 'COM_KUNENA_A_USE_MAIL_TEMPLATES'],
            'popup option'             => [$admin, 'COM_KUNENA_A_POPUP_NOTIFICATIONS'],
            'alias option'             => [$admin, 'COM_KUNENA_SEF_CATEGORY_ALIAS_ID'],
            'link cards option'        => [$admin, 'COM_KUNENA_CONFIGURATION_LINKPREVIEW'],
            'spotify option'           => [$admin, 'COM_KUNENA_CONFIGURATION_AUTOEMBEDSPOTIFY'],
            'deezer option'            => [$admin, 'COM_KUNENA_CONFIGURATION_AUTOEMBEDDEEZER'],
        ];
    }

    #[DataProvider('newTexts')]
    public function testTheTextOfAFeatureIsThere(string $file, string $key): void
    {
        $strings = Files::language($file);

        $this->assertArrayHasKey($key, $strings);
        $this->assertNotSame('', $strings[$key]);
    }

    public function testTheTextsOfThePopupsTakeTheNameOfTheAuthor(): void
    {
        $strings = Files::language('src/site/language/en-GB/com_kunena.ini');

        foreach (['COM_KUNENA_POPUP_MENTION', 'COM_KUNENA_POPUP_REPLY_OWN', 'COM_KUNENA_POPUP_REPLY_SUBSCRIBED', 'COM_KUNENA_POPUP_NEW_TOPIC'] as $key) {
            $this->assertStringContainsString('%s', $strings[$key], $key . ' has no place for the name.');
        }

        $this->assertStringContainsString('%d', $strings['COM_KUNENA_POPUP_MORE']);
    }

    /**
     * Every text which the code asks for (Text::_('COM_KUNENA_...'), Text::sprintf(...)) is in an English language file of the
     * package: a missing one is shown as its key (for example the messages of the steps of an update in the installer).
     * Keys which the code puts together (a prefix which ends with "_") and code in comments are left out.
     */
    public function testEveryTextOfTheCodeIsInALanguageFile(): void
    {
        $keys = [];

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(Files::path('src'), \FilesystemIterator::SKIP_DOTS)) as $file) {
            if ($file->getExtension() === 'ini' && str_contains(str_replace('\\', '/', $file->getPathname()), '/en-GB/')) {
                foreach (file($file->getPathname()) ?: [] as $line) {
                    if (preg_match('/^\s*([A-Z0-9_]+)\s*=/', $line, $match)) {
                        $keys[$match[1]] = true;
                    }
                }
            }
        }

        $missing = [];

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(Files::path('src'), \FilesystemIterator::SKIP_DOTS)) as $file) {
            $name = str_replace('\\', '/', $file->getPathname());

            if ($file->getExtension() !== 'php' || str_contains($name, '/External/')) {
                continue;
            }

            foreach (file($file->getPathname()) ?: [] as $number => $line) {
                // Code in a comment does not ask for a text.
                $code = preg_replace('#(//|^\s*\*|^\s*/\*).*$#', '', $line);

                if (!preg_match_all("/Text::(?:_|sprintf|plural|script)\(\s*'((?:COM|PLG)_KUNENA[A-Z0-9_]*)'/", (string) $code, $matches)) {
                    continue;
                }

                foreach ($matches[1] as $key) {
                    if (!str_ends_with($key, '_') && !isset($keys[$key])) {
                        $missing[] = $key . ' (' . substr($name, \strlen(str_replace('\\', '/', Files::path('src'))) + 1) . ':' . ($number + 1) . ')';
                    }
                }
            }
        }

        $this->assertSame([], $missing, 'Texts which the code uses and no English language file has.');
    }
}
