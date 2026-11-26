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

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * While a chunk of an upload is read, the time limit is 8 seconds for every 8 kB (a transfer which stalls ends). After the chunk
 * the request has the time limit of the server again. It got 25 seconds whatever the server allows: the check of the file, a
 * picture made smaller and the save of the attachment stopped after that on a server with a longer limit (a note at the 8 seconds
 * said "not sure if it works").
 */
class KunenaUploadTimeLimitTest extends UnitTestCase
{
    public function testTheTimeLimitOfTheServerIsGivenBackAfterTheChunk(): void
    {
        $source = Files::read('src/libraries/kunena/src/Upload/KunenaUpload.php');

        $this->assertSame(1, preg_match('/public function ajaxUpload\(array \$options\): array\s*\{(.*?)\n    \}\n/s', $source, $m));

        $read    = strpos($m[1], "\$timeLimit = (int) \\ini_get('max_execution_time');");
        $try     = strpos($m[1], 'try {');
        $chunk   = strpos($m[1], '@set_time_limit(8);');
        $restore = strpos($m[1], '@set_time_limit($timeLimit);');

        $this->assertIsInt($read, 'The time limit of the server is read.');
        $this->assertIsInt($chunk);
        $this->assertIsInt($restore, 'The time limit of the server is set again.');
        $this->assertLessThan($try, $read, 'Read before the chunk is handled: a refused chunk gives it back too.');
        $this->assertLessThan($restore, $chunk);
        $this->assertSame(2, substr_count($m[1], 'set_time_limit('), 'No other time limit in the upload.');
    }
}
