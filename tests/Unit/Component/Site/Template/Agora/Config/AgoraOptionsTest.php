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

namespace Kunena\Tests\Unit\Component\Site\Template\Agora\Config;

use FilesystemIterator;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SimpleXMLElement;

/**
 * The template Agora is built on Aurelia: it has every option of Aurelia (with the same start values), its own options are read by
 * its code, and each layout of Agora replaces a layout of Aurelia (the others are taken from Aurelia).
 */
class AgoraOptionsTest extends UnitTestCase
{
    private const AGORA = 'src/site/template/agora';

    private const AURELIA = 'src/site/template/aurelia';

    /**
     * The fields of a config.xml, by name.
     *
     * @return array<string,SimpleXMLElement>
     */
    private static function fields(string $template): array
    {
        $fields = [];

        foreach (simplexml_load_string(Files::read("$template/config/config.xml"))->xpath('//field') as $field) {
            $fields[(string) $field['name']] ??= $field;
        }

        return $fields;
    }

    /**
     * The files of Agora (relative to its folder) with one of the extensions.
     *
     * @return string[]
     */
    private static function files(string $extensions): array
    {
        $root  = str_replace('\\', '/', Files::path(self::AGORA));
        $files = [];

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)) as $file) {
            $path = str_replace('\\', '/', $file->getPathname());

            if (preg_match('/\.(' . $extensions . ')$/', $path)) {
                $files[] = substr($path, \strlen($root) + 1);
            }
        }

        sort($files);

        return $files;
    }

    public function testAgoraHasEveryOptionOfAurelia(): void
    {
        $agora = self::fields(self::AGORA);

        foreach (self::fields(self::AURELIA) as $name => $field) {
            $this->assertArrayHasKey($name, $agora, "Agora does not have the option $name of Aurelia.");
            $this->assertSame((string) $field['type'], (string) $agora[$name]['type'], "The option $name has another type in Agora.");
            $this->assertSame((string) $field['default'], (string) $agora[$name]['default'], "The option $name has another start value in Agora.");
        }
    }

    public function testTheOwnOptionsOfAgoraAreReadByItsCode(): void
    {
        $code = '';

        foreach (self::files('php|js') as $file) {
            $code .= Files::read(self::AGORA . "/$file");
        }

        $own = array_diff(array_keys(self::fields(self::AGORA)), array_keys(self::fields(self::AURELIA)));

        $this->assertNotEmpty($own);

        foreach ($own as $name) {
            $this->assertMatchesRegularExpression('/->get\(\s*[\'"]' . preg_quote($name, '/') . '[\'"]/', $code, "No code of Agora reads its option $name.");
        }

        preg_match_all('/params->get\(\s*[\'"](\w+)[\'"]/', $code, $matches);

        $this->assertSame([], array_values(array_diff($matches[1], array_keys(self::fields(self::AGORA)))), 'Agora reads options which its config.xml does not have.');
    }

    public function testEachLayoutOfAgoraReplacesALayoutOfAurelia(): void
    {
        $layouts = array_filter(self::files('php'), static fn ($file) => str_starts_with($file, 'layouts/') || str_starts_with($file, 'pages/'));

        $this->assertNotEmpty($layouts);

        foreach ($layouts as $file) {
            $this->assertFileExists(Files::path(self::AURELIA . "/$file"), "The layout $file of Agora has no layout of Aurelia to replace (a wrong name?).");
        }
    }

    public function testAgoraTakesTheMissingFilesFromAurelia(): void
    {
        $code = Files::read(self::AGORA . '/template.php');

        $this->assertStringContainsString('class KunenaTemplateAgora extends KunenaTemplateAurelia', $code);
        $this->assertMatchesRegularExpression('/protected mixed \$default = \[\'aurelia\'\];/', $code);
        $this->assertStringContainsString('<name>Agora</name>', Files::read(self::AGORA . '/config/template.xml'));
    }
}
