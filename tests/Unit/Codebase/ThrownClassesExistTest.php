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

namespace Kunena\Tests\Unit\Codebase;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Every exception of Kunena which the library throws is a class which exists. The sample data threw KunenaInstallerException,
 * which no file defines: a failure of its last query ended in "Class not found" instead of the message of the failure.
 */
class ThrownClassesExistTest extends UnitTestCase
{
    public function testEveryThrownExceptionOfKunenaExists(): void
    {
        $this->requireJoomla();

        $root    = Files::path('src/libraries/kunena/src');
        $missing = [];
        $checked = 0;

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            // The code without its comments (a block of old code in a comment throws nothing).
            $source = '';

            foreach (token_get_all((string) file_get_contents($file->getPathname())) as $token) {
                if (!\is_array($token)) {
                    $source .= $token;
                } elseif ($token[0] !== T_COMMENT && $token[0] !== T_DOC_COMMENT) {
                    $source .= $token[1];
                }
            }

            if (!preg_match_all('/throw new (\\\\?[A-Z][\w\\\\]*)\(/', $source, $thrown)) {
                continue;
            }

            preg_match('/^namespace ([\w\\\\]+);/m', $source, $namespace);
            preg_match_all('/^use ([\w\\\\]+?)(?: as (\w+))?;/m', $source, $uses, PREG_SET_ORDER);

            $imports = [];

            foreach ($uses as $use) {
                $imports[$use[2] ?? substr((string) strrchr('\\' . $use[1], '\\'), 1)] = $use[1];
            }

            foreach (array_unique($thrown[1]) as $name) {
                if ($name[0] === '\\') {
                    $class = substr($name, 1);
                } else {
                    $first = explode('\\', $name)[0];
                    $class = isset($imports[$first])
                        ? $imports[$first] . substr($name, \strlen($first))
                        : ($namespace[1] ?? '') . '\\' . $name;
                }

                // The exceptions of Kunena, and the classes of the namespace of the file (a name without an import).
                if (!str_starts_with($class, 'Kunena\\')) {
                    continue;
                }

                $checked++;

                // A class of the file itself (KunenaSchemaException at the end of the file of the schema) is there with the file.
                if (preg_match('/^(?:final |abstract )?class ' . preg_quote(substr((string) strrchr('\\' . $class, '\\'), 1), '/') . '\b/m', $source)) {
                    continue;
                }

                if (!class_exists($class)) {
                    $missing[] = substr($file->getPathname(), \strlen($root) + 1) . ': ' . $class;
                }
            }
        }

        $this->assertGreaterThan(10, $checked, 'The thrown exceptions are found.');
        $this->assertSame([], $missing);
    }
}
