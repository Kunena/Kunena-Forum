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

namespace Kunena\Tests\Integration\Libraries\Kunena\Tools;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Tools\KunenaImageOptimizer;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The tool "Optimize images" saves an image as WebP under the name with the extension .webp: when an attachment of the same
 * folder had that name already, its file was replaced by the other picture (and deleting one of the two attachments later
 * deleted the file of both).
 */
class KunenaImageOptimizerNameTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private array $saved = [];

    /**
     * The folder of the test, below the folder tmp of the site (removed after the test).
     */
    private string $folder = '';

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JPATH_ROOT') || !\function_exists('imagewebp')) {
            $this->markTestSkipped('The framework of Joomla with GD and WebP is needed.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);
        $config = (new ReflectionClass(KunenaConfig::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(KunenaConfig::class, 'config'))->setValue($config, ['imageWidth' => 100, 'imageHeight' => 100]);

        $this->saved = [
            'container' => Factory::$container,
            'config'    => $this->replace(KunenaConfig::class, 'instance', $config),
        ];
        Factory::$container = $container;

        $this->folder = 'tmp/kd-optimizer-test-' . bin2hex(random_bytes(4));
        mkdir(JPATH_ROOT . '/' . $this->folder, 0755, true);

        // A wide photo (a candidate: wider than 100), and a small picture of another attachment which has the WebP name already.
        $this->image(JPATH_ROOT . '/' . $this->folder . '/holiday.png', 400, 300, [200, 30, 30], 'png');
        $this->image(JPATH_ROOT . '/' . $this->folder . '/holiday.webp', 50, 50, [30, 30, 200], 'webp');

        $this->insertRows('#__kunena_attachments', [
            ['id' => 1, 'mesid' => 10, 'userid' => 42, 'folder' => $this->folder, 'filetype' => 'image/png', 'filename' => 'holiday.png', 'filename_real' => 'holiday.png', 'size' => (int) filesize(JPATH_ROOT . '/' . $this->folder . '/holiday.png'), 'hash' => ''],
            ['id' => 2, 'mesid' => 11, 'userid' => 42, 'folder' => $this->folder, 'filetype' => 'image/webp', 'filename' => 'holiday.webp', 'filename_real' => 'holiday.webp', 'size' => (int) filesize(JPATH_ROOT . '/' . $this->folder . '/holiday.webp'), 'hash' => ''],
        ]);
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            Factory::$container = $this->saved['container'];
            $this->replace(KunenaConfig::class, 'instance', $this->saved['config']);
        }

        if ($this->folder !== '' && is_dir(JPATH_ROOT . '/' . $this->folder)) {
            foreach (glob(JPATH_ROOT . '/' . $this->folder . '/{,.}*', GLOB_BRACE) ?: [] as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            rmdir(JPATH_ROOT . '/' . $this->folder);
        }

        parent::tearDown();
    }

    /**
     * An image of one colour with noise (so that it is heavy enough to be smaller as WebP).
     */
    private function image(string $path, int $width, int $height, array $rgb, string $type): void
    {
        $image = imagecreatetruecolor($width, $height);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                imagesetpixel($image, $x, $y, imagecolorallocate($image, ($rgb[0] + mt_rand(0, 40)) % 256, ($rgb[1] + mt_rand(0, 40)) % 256, ($rgb[2] + mt_rand(0, 40)) % 256));
            }
        }

        $type === 'png' ? imagepng($image, $path) : imagewebp($image, $path);
        imagedestroy($image);
    }

    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }

    public function testTheWebpOfAnImageDoesNotReplaceTheFileOfAnotherAttachment(): void
    {
        $other = md5_file(JPATH_ROOT . '/' . $this->folder . '/holiday.webp');

        $result = KunenaImageOptimizer::optimizeOnSite([1], true);

        $this->assertSame(1, $result['done']);

        $rows = array_column($this->fetchAll('SELECT id, filename FROM #__kunena_attachments ORDER BY id'), 'filename', 'id');

        // The optimized image has a name of its own, the other attachment keeps its file.
        $this->assertSame('holiday-1.webp', $rows[1]);
        $this->assertSame('holiday.webp', $rows[2]);
        $this->assertSame($other, md5_file(JPATH_ROOT . '/' . $this->folder . '/holiday.webp'));
        $this->assertFileExists(JPATH_ROOT . '/' . $this->folder . '/holiday-1.webp');
        $this->assertFileDoesNotExist(JPATH_ROOT . '/' . $this->folder . '/holiday.png');
    }
}
