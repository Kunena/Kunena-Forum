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

namespace Kunena\Tests\Unit\Component\Site\Controllers;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The messages after subscribing to a category and after unsubscribing from it held the name of the category as it is stored.
 * Joomla prints a message as html, so markup in the name of a category (which its administrator sets) ran for the member who
 * subscribed. The other messages of the controller already encoded the name.
 */
class CategoryControllerSubscribeMessagesTest extends UnitTestCase
{
    private function task(string $name): string
    {
        $source = Files::read('src/site/src/Controllers/CategoryController.php');

        $this->assertSame(1, preg_match('/public function ' . $name . '\(\)\s*\{(.*?)\n    \}\n/s', $source, $m), $name);

        return $m[1];
    }

    public static function messages(): array
    {
        return [
            ['subscribe', 'COM_KUNENA_CATEGORY_USER_SUBCRIBED_WITH_CHILDREN'],
            ['subscribe', 'COM_KUNENA_CATEGORY_USER_SUBCRIBED'],
            ['unsubscribe', 'COM_KUNENA_GEN_CATEGORY_NAME_UNSUBCRIBED'],
            ['unsubscribe', 'COM_KUNENA_CATEGORY_NAME_MODERATOR_UNSUBCRIBED_USER'],
        ];
    }

    #[DataProvider('messages')]
    public function testNameOfTheCategoryIsEncodedInTheMessage(string $task, string $key): void
    {
        $body = $this->task($task);

        $this->assertSame(1, preg_match("/enqueueMessage\\(Text::(?:sprintf|plural)\\('" . $key . "',([^;]*)\\), 'success'\\);/", $body, $m), $key);
        $this->assertStringNotContainsString('$category->name', $m[1], 'The name is not put into the message as it is stored.');
        $this->assertStringContainsString('$name', $m[1]);

        $encoded = strpos($body, "\$name = htmlspecialchars((string) \$category->name, ENT_QUOTES, 'UTF-8');");

        $this->assertIsInt($encoded);
        $this->assertLessThan(strpos($body, "'" . $key . "'"), $encoded, 'The name is encoded before the message is made.');
    }

    public function testNoMessageOfTheControllerHoldsTheStoredName(): void
    {
        $source = Files::read('src/site/src/Controllers/CategoryController.php');

        // The name inside htmlspecialchars() is fine; the name as an argument of the text is not.
        $this->assertSame(0, preg_match('/enqueueMessage\([^;]*(?<!htmlspecialchars\()(?<!\(string\) )\$category->name[,)]/', $source));
    }
}
