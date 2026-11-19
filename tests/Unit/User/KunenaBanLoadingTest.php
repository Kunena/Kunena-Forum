<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Tests
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\User;

use PHPUnit\Framework\TestCase;

class KunenaBanLoadingTest extends TestCase
{
    private const SRC = __DIR__ . '/../../../src/libraries/kunena/src';

    public function testLoadingWithoutAnIdDoesNotMarkTheBanAsExisting(): void
    {
        $source = file_get_contents(self::SRC . '/User/KunenaBan.php');

        $this->assertIsString($source);
        $this->assertStringContainsString('$exists = false;', $source);
        $this->assertMatchesRegularExpression(
            '/if\s*\(!empty\(\$id\)\)\s*\{\s*\$exists\s*=\s*\(bool\)\s*\$table->load\(\$id\);/s',
            $source
        );
        $this->assertStringContainsString('$this->_exists = $exists;', $source);
    }

    public function testLoadingAnExistingBanReceivesTheBindResult(): void
    {
        $source = file_get_contents(self::SRC . '/Tables/KunenaUserBans.php');

        $this->assertIsString($source);
        $this->assertStringContainsString('return parent::bind($data, $ignore);', $source);
    }
}
