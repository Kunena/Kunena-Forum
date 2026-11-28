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
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The e-mail field of a guest takes a whole address (254 characters, the column keeps 255): the forms stopped at 45 characters, the
 * quick reply at the bottom at 35, so a guest with a longer address could not post where the e-mail is required.
 */
class GuestEmailLengthTest extends UnitTestCase
{
    public static function forms(): array
    {
        return [
            'the reply at the bottom' => ['src/site/template/aurelia/layouts/message/edit/full.php', 1],
            'the quick reply'         => ['src/site/template/aurelia/layouts/message/edit/quickreply.php', 2],
            'the form of a post'      => ['src/site/template/aurelia/layouts/topic/edit/default.php', 1],
        ];
    }

    #[DataProvider('forms')]
    public function testTheFieldTakesAWholeAddress(string $file, int $fields): void
    {
        // The lines of the fields (a field is on one line; its PHP has a ">" of its own).
        $lines = array_values(array_filter(preg_split('/\R/', Files::read($file)), static fn ($line) => str_contains($line, 'name="email"')));

        $this->assertCount($fields, $lines);

        foreach ($lines as $field) {
            $this->assertStringContainsString('maxlength="254"', $field);
        }
    }
}
