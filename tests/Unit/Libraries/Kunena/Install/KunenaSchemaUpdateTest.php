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

namespace Kunena\Tests\Unit\Libraries\Kunena\Install;

use Kunena\Forum\Libraries\Install\KunenaSchemaUpdate as S;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * Git Install updates the database of a development site from the update files of the checkout: only from the version of
 * the schema of the site onwards, so that an old file never adds a column which a later version removed.
 */
class KunenaSchemaUpdateTest extends UnitTestCase
{
    private const FILES = ['x/6.0.0-BETA1-DEV.sql', 'x/7.1.0-BETA3-DEV.sql', 'x/8.0.0-DEV.sql', 'x/7.1.0-BETA1-DEV.sql'];

    public function testOnlyTheFilesFromTheVersionOfTheSite(): void
    {
        // The file of the version itself too: it grows while the version is developed.
        $this->assertSame(['x/8.0.0-DEV.sql'], S::filesFrom(self::FILES, '8.0.0-DEV'));
        $this->assertSame(['x/7.1.0-BETA3-DEV.sql', 'x/8.0.0-DEV.sql'], S::filesFrom(self::FILES, '7.1.0-BETA3-DEV'));
    }

    public function testAllTheFilesInTheOrderOfTheVersionsWithoutAVersion(): void
    {
        $this->assertSame(['x/6.0.0-BETA1-DEV.sql', 'x/7.1.0-BETA1-DEV.sql', 'x/7.1.0-BETA3-DEV.sql', 'x/8.0.0-DEV.sql'], S::filesFrom(self::FILES, ''));
        $this->assertSame('8.0.0-DEV', S::latest(self::FILES));
        $this->assertSame('', S::latest([]));
    }

    /**
     * The newest update file of the checkout is the one of the version which is developed.
     */
    public function testTheNewestFileOfTheCheckout(): void
    {
        $this->assertSame('8.0.0-DEV', S::latest((array) glob(Files::path('src/admin/sql/updates/mysql') . '/*.sql')));
    }
}
