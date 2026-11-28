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

namespace Kunena\Tests\Unit\Component\Site\Template\Aurelia;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The suggestions of similar topics come back for a subject which was shortened and typed again (the list was emptied, and the same
 * subject was "asked already"), and after a failed request. (The script runs in a browser: its code is read.)
 */
class SimilarTopicsAgainTest extends UnitTestCase
{
    public function testTheSameSubjectIsAskedAgainAfterAnEmptyListOrAFailure(): void
    {
        $script = Files::read('src/site/template/aurelia/assets/js/similartopics.js');

        $this->assertSame(4, substr_count($script, "last = '';"), 'the declaration and the three resets');
    }
}
