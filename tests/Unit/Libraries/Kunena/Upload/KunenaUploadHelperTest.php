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

namespace Kunena\Tests\Unit\Libraries\Kunena\Upload;

use Kunena\Forum\Libraries\Upload\KunenaUploadHelper as T;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * An uploaded file gets a safe name in lower case which is not taken in its folder.
 */
class KunenaUploadHelperTest extends UnitTestCase
{
    public function testAFreeName(): void
    {
        $taken  = ['smile.png' => true, 'smile-2.png' => true, 'readme' => true];
        $exists = static fn (string $name) => isset($taken[$name]);

        $this->assertSame('grin.png', T::uniqueName('Grin.PNG', $exists));
        $this->assertSame('smile-3.png', T::uniqueName('smile.png', $exists));
        $this->assertSame('readme-2', T::uniqueName('README', $exists));
    }

    public function testAnUnsafeName(): void
    {
        $none = static fn () => false;

        // The folders of the name are gone (File::makeSafe()): it stays in the folder of the upload.
        $this->assertSame('etcpasswd', T::uniqueName('../../etc/passwd', $none));
        $this->assertNull(T::uniqueName('...', $none));
        $this->assertNull(T::uniqueName('', $none));
    }

    public function testNoFreeNameLeft(): void
    {
        $this->assertNull(T::uniqueName('smile.png', static fn () => true));
    }
}
