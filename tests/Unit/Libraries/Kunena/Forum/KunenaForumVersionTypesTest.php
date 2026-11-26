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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum;

use Kunena\Forum\Libraries\Forum\KunenaForum;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The major version of Kunena is its number ("8.0"): the method was declared boolean, so PHP gave true. And the date of the
 * version is a string also when it is asked first: it starts as null and the method looked for false, so it gave null back,
 * which is an error of PHP for a method which gives a string.
 */
class KunenaForumVersionTypesTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        foreach (['version', 'version_major', 'version_date', 'version_name'] as $name) {
            $this->saved[$name] = (new ReflectionProperty(KunenaForum::class, $name))->getValue();
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->saved as $name => $value) {
            (new ReflectionProperty(KunenaForum::class, $name))->setValue(null, $value);
        }
    }

    public function testTheMajorVersionIsItsNumber(): void
    {
        (new ReflectionProperty(KunenaForum::class, 'version_major'))->setValue(null, '8.0');

        $this->assertSame('8.0', KunenaForum::versionMajor());
    }

    public function testTheDateOfTheVersionIsAStringWhenItIsAskedFirst(): void
    {
        if (!is_file(JPATH_MANIFESTS . '/packages/pkg_kunena.xml')) {
            $this->markTestSkipped('The Joomla of the tests has no manifest of Kunena.');
        }

        (new ReflectionProperty(KunenaForum::class, 'version_date'))->setValue(null, null);

        $this->assertNotSame('', KunenaForum::versionDate());
    }
}
