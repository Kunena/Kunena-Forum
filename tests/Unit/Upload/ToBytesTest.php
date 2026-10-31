<?php

/**
 * Kunena Component
 *
 * Regression test for KunenaUpload::toBytes(). It turned "128M" into 128 (the number of the unit, not bytes) and "-1" (no
 * limit, which is the usual memory_limit of the command line and of many hosts) into NAN, which then went through min() and
 * max() of the size checks.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Upload;

use Kunena\Forum\Libraries\Upload\KunenaUpload;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ToBytesTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/../../../src/libraries/kunena/src/Image/KunenaImageHelper.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/Upload/KunenaUpload.php';
    }

    public static function values(): array
    {
        return [
            'megabytes'          => ['128M', 128 * 1024 * 1024],
            'lower case'         => ['64m', 64 * 1024 * 1024],
            'gigabytes'          => ['1G', 1024 * 1024 * 1024],
            'kilobytes'          => ['512K', 512 * 1024],
            'plain bytes'        => ['1048576', 1048576],
            'with b'             => ['2MB', 2 * 1024 * 1024],
            'padded'             => [' 8M ', 8 * 1024 * 1024],
            'no limit'           => ['-1', PHP_INT_MAX],
            'zero'               => ['0', PHP_INT_MAX],
            'empty'              => ['', PHP_INT_MAX],
            'not a size'         => ['abc', PHP_INT_MAX],
        ];
    }

    #[DataProvider('values')]
    public function testToBytes(string $value, int $expected): void
    {
        $bytes = KunenaUpload::toBytes($value);

        $this->assertIsInt($bytes);
        $this->assertSame($expected, $bytes);
    }

    public function testNoLimitDoesNotDisturbTheSmallestLimit(): void
    {
        foreach ([['64M', '64M', '-1'], ['-1', '64M', '64M'], ['-1', '-1', '8M']] as $order) {
            $smallest = min(array_map([KunenaUpload::class, 'toBytes'], $order));

            $this->assertGreaterThan(0, $smallest);
            $this->assertFalse(is_nan((float) $smallest));
            $this->assertLessThan(PHP_INT_MAX, $smallest, implode(' ', $order));
        }
    }

    public function testResultNeverIsNan(): void
    {
        foreach (['-1', '0', '', 'x', '1e9', '9999999999999999999G'] as $value) {
            $this->assertFalse(is_float(KunenaUpload::toBytes($value)) && is_nan(KunenaUpload::toBytes($value)), $value);
        }
    }
}
