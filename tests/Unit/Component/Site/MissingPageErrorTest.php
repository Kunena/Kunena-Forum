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

namespace Kunena\Tests\Unit\Component\Site;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The error of a page which does not exist said the name of the class which was built from the request. A visitor gets a general
 * message; only with the debug mode of Joomla and the one of Kunena on, the developer sees the name of the missing class.
 */
class MissingPageErrorTest extends UnitTestCase
{
    private function missingClassBlock(): string
    {
        $source = Files::read('src/libraries/kunena/src/Request/KunenaRequest.php');

        $this->assertSame(1, preg_match('/if \(!class_exists\(\$classnamespaced\)\) \{(.*?)\n        \}\n/s', $source, $m));

        return $m[1];
    }

    public function testAVisitorDoesNotSeeTheNameOfTheClass(): void
    {
        $block = $this->missingClassBlock();

        $this->assertStringContainsString("throw new InvalidArgumentException('The page does not exist.', 404);", $block);

        // The message with the class is in the debug branch, and only there.
        $this->assertSame(1, preg_match_all('/InvalidArgumentException\([^;]*\$classnamespaced/', $block));
        $this->assertSame(
            1,
            preg_match('/if \(JDEBUG && KunenaConfig::getInstance\(\)->debug\) \{\s*throw new InvalidArgumentException\([^;]*\$classnamespaced[^;]*;\s*\}/s', $block)
        );
    }

    public static function debugModes(): array
    {
        return [
            'the debug mode of Kunena on'  => [1, "In the KunenaRequest class the controller Kunena\\Forum\\Site\\Controller\\Nosuch\\Thing\\NosuchThing doesn't exist."],
            'the debug mode of Kunena off' => [0, 'The page does not exist.'],
        ];
    }

    /**
     * The branch runs only with JDEBUG, which the unit tests define false: a PHP process of its own has it true, and a configuration
     * with only the option debug. KunenaConfig->debug (an arrow on a class name) was a fatal error there.
     */
    #[DataProvider('debugModes')]
    public function testTheMessageWithTheDebugModeOfJoomla(int $debug, string $expected): void
    {
        $code = 'namespace Kunena\Forum\Libraries\Config { class KunenaConfig { public $debug = ' . $debug . ';'
            . ' public static function getInstance() { return new self(); } } }'
            . ' namespace { define("_JEXEC", 1); define("JDEBUG", true);'
            . ' require ' . var_export(Files::path('src/libraries/kunena/src/Request/KunenaRequest.php'), true) . ';'
            . ' try { \Kunena\Forum\Libraries\Request\KunenaRequest::factory("Nosuch/Thing"); echo "no exception"; }'
            . ' catch (\Throwable $e) { echo get_class($e), "|", $e->getCode(), "|", $e->getMessage(); } }';

        $script = tempnam(sys_get_temp_dir(), 'kreq') . '.php';
        file_put_contents($script, '<?php ' . $code);

        try {
            $output = shell_exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($script) . ' 2>&1');
        } finally {
            @unlink($script);
        }

        $this->assertSame('InvalidArgumentException|404|' . $expected, trim((string) $output));
    }

    public function testTheNameOfTheClassHasOnlyTheCharactersOfAClass(): void
    {
        // The request is cleaned before the name is built, so the debug message has no markup.
        $this->assertStringContainsString(
            "preg_replace('/[^a-z0-9_]+/i', ' ', (string) \$path)",
            Files::read('src/libraries/kunena/src/Request/KunenaRequest.php')
        );
    }
}
