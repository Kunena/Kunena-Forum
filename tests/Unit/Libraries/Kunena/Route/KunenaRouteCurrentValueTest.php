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

namespace Kunena\Tests\Unit\Libraries\Kunena\Route;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionProperty;

/**
 * The values of the query of the request which stay in the current address of Kunena (the links of the page are made from it):
 * letters, digits, "_" and spaces. An array (&catids[]=2) was taken whole (a note: "we need to find a way to here deal with
 * arrays"), whatever it had in it; it keeps its good values now.
 */
class KunenaRouteCurrentValueTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();
        $this->loadRoute();
    }

    /**
     * Loading KunenaRoute runs KunenaRoute::initialize(), which needs the configuration and an application: an empty configuration
     * and an application of the command line, for which it stops at once, both taken back afterwards.
     */
    private function loadRoute(): void
    {
        if (class_exists(KunenaRoute::class, false)) {
            return;
        }

        $config      = new ReflectionProperty(KunenaConfig::class, 'instance');
        $application = Factory::$application;
        $cli         = $this->createStub(CMSApplicationInterface::class);
        $cli->method('isClient')->willReturn(true);

        $config->setValue(null, $this->bare(KunenaConfig::class));
        Factory::$application = $cli;

        try {
            class_exists(KunenaRoute::class);
        } finally {
            $config->setValue(null, null);
            Factory::$application = $application;
        }
    }

    public static function values(): array
    {
        return [
            'a word'                    => ['topics', 'topics'],
            'a number'                  => ['20', '20'],
            'words with a space'        => ['my topics', 'my topics'],
            'an empty value'            => ['', ''],
            'a quote'                   => ['a"b', null],
            'a tag'                     => ['<script>', null],
            'an array of ids'           => [['2', '5'], ['2', '5']],
            'an array with named keys'  => [['first' => 'a', 'second' => 'b'], ['first' => 'a', 'second' => 'b']],
            'an array with a bad value' => [['2', '"><script>', '5'], [0 => '2', 2 => '5']],
            'an array with a bad key'   => [['a"b' => '2', 'ok' => '3'], ['ok' => '3']],
            'an array in an array'      => [[['2'], '5'], [1 => '5']],
            'an array of bad values'    => [['<b>', '"'], null],
            'an empty array'            => [[], null],
        ];
    }

    #[DataProvider('values')]
    public function testOnlyTheGoodValuesStay(mixed $value, mixed $kept): void
    {
        $this->assertSame($kept, KunenaRoute::cleanCurrentValue($value));
    }
}
