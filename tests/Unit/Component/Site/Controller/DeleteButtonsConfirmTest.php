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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The delete and permanent-delete buttons of a topic and of a message ask the browser to confirm first: each one sets a confirm
 * text, which the button widget turns into onclick="return confirm(...)". The undelete buttons ask nothing.
 */
class DeleteButtonsConfirmTest extends UnitTestCase
{
    private const TOPIC = 'src/site/src/Controller/Topic/Item/Actions/TopicItemActionsDisplay.php';

    private const MESSAGE = 'src/site/src/Controller/Message/Item/Actions/MessageItemActionsDisplay.php';

    public static function files(): array
    {
        return [
            'topic'   => [self::TOPIC, 'topicButtons', 'COM_KUNENA_TOPIC_DELETE_CONFIRM', 'COM_KUNENA_TOPIC_PERMDELETE_CONFIRM'],
            'message' => [self::MESSAGE, 'messageButtons', 'COM_KUNENA_MESSAGE_DELETE_CONFIRM', 'COM_KUNENA_MESSAGE_PERMDELETE_CONFIRM'],
        ];
    }

    #[DataProvider('files')]
    public function testTheDeleteButtonsSetTheirConfirmTexts(string $file, string $buttons, string $delete, string $permdelete): void
    {
        $content = Files::read($file);

        $this->assertStringContainsString("->set('confirm', Text::_('" . $delete . "'))", $content);
        $this->assertStringContainsString("->set('confirm', Text::_('" . $permdelete . "'))", $content);
    }

    #[DataProvider('files')]
    public function testEveryDeleteButtonAsksToConfirm(string $file, string $buttons, string $delete, string $permdelete): void
    {
        $expected = [
            'delete'              => $delete,
            'delete_dropdown'     => $delete,
            'permdelete'          => $permdelete,
            'permdelete_dropdown' => $permdelete,
        ];

        $found = [];

        foreach ($this->buttonSets($file, $buttons) as [$key, $code]) {
            if (!isset($expected[$key])) {
                continue;
            }

            $found[$key] = true;

            $this->assertStringContainsString(
                "->set('confirm', Text::_('" . $expected[$key] . "'))",
                $code,
                'The button ' . $key . ' in ' . $file . ' does not ask to confirm.'
            );
        }

        $this->assertSame(array_keys($expected), array_keys(array_intersect_key($expected, $found)), 'A delete button is missing in ' . $file . '.');
    }

    #[DataProvider('files')]
    public function testTheUndeleteButtonsAskNothing(string $file, string $buttons, string $delete, string $permdelete): void
    {
        $count = 0;

        foreach ($this->buttonSets($file, $buttons) as [$key, $code]) {
            if ($key !== 'undelete' && $key !== 'undelete_dropdown') {
                continue;
            }

            $count++;

            $this->assertStringNotContainsString("'confirm'", $code, 'The button ' . $key . ' in ' . $file . ' asks to confirm.');
        }

        $this->assertSame(2, $count, 'The undelete buttons are not in ' . $file . '.');
    }

    /**
     * The buttons set with a literal key in a file, each with its code up to the next set of a button.
     *
     * @return  array<int, array{0: string, 1: string}>
     */
    private function buttonSets(string $file, string $buttons): array
    {
        $parts = explode('$this->' . $buttons . '->set(', Files::read($file));
        array_shift($parts);

        $sets = [];

        foreach ($parts as $part) {
            if (preg_match("/^\s*'([a-z_]+)'/", $part, $match)) {
                $sets[] = [$match[1], $part];
            }
        }

        return $sets;
    }
}
