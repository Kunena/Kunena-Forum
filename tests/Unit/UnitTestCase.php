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

namespace Kunena\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

/**
 * Base unit test case for common behaviour across the unit tests of Kunena.
 */
abstract class UnitTestCase extends TestCase
{
    /**
     * Skip the test when the framework of Joomla is not loaded (see the bootstrap: KUNENA_TEST_JOOMLA).
     *
     * @return  void
     */
    protected function requireJoomla(): void
    {
        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }
    }

    /**
     * Call a protected or private method (a rule of a class which is only used inside the class).
     *
     * @param   object|string  $target     The object, or the name of the class for a static method.
     * @param   string         $method     The name of the method.
     * @param   mixed          ...$arguments  The arguments.
     *
     * @return  mixed
     */
    protected static function invoke(object|string $target, string $method, mixed ...$arguments): mixed
    {
        $reflection = new ReflectionMethod($target, $method);

        return $reflection->invoke(\is_object($target) ? $target : null, ...$arguments);
    }

    /**
     * An object of a class without its constructor (the constructor needs Joomla or the database).
     *
     * @param   string  $class       The class.
     * @param   array   $properties  Values of properties (public or not) to set.
     *
     * @return  object
     */
    protected static function bare(string $class, array $properties = []): object
    {
        $reflection = new ReflectionClass($class);
        $object     = $reflection->newInstanceWithoutConstructor();

        foreach ($properties as $name => $value) {
            if ($reflection->hasProperty($name)) {
                $property = $reflection->getProperty($name);
                $property->setValue($object, $value);
            } else {
                $object->$name = $value;
            }
        }

        return $object;
    }
}
