<?php

/**
 * Kunena Component
 *
 * One stand-in for KunenaConfig for the tests which render layouts: it answers every setting with a plain default, so the tests
 * do not each need their own class (a class can be aliased only once per run).
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode\Fixtures;

class GenericConfig
{
    /** @var array<string, mixed> */
    public static array $values = [
        'lightbox'         => 0,
        'imageHeight'      => 800,
        'plainEmail'       => 0,
        'mailFull'         => 0,
        'emailHeader'      => 'media/header.png',
        'emailHeaderSizeX' => 100,
        'emailHeaderSizeY' => 50,
        'boardTitle'       => 'The Board',
    ];

    public static function getInstance(): self
    {
        return new self();
    }

    public function __get($name)
    {
        return self::$values[$name] ?? null;
    }

    public function __isset($name)
    {
        return isset(self::$values[$name]);
    }
}
