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

use Closure;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The error page of the forum printed the status and the message of the exception as html. The messages are texts of the forum
 * today, but a message can hold a value of the request (the task, the name of a class): it is text on the page.
 */
class WidgetErrorTest extends UnitTestCase
{
    private const LAYOUT = 'src/site/template/aurelia/layouts/widget/error/default.php';

    /**
     * The heading of the layout (the part before the list of reasons, which needs the language of Joomla).
     */
    private function heading(object $layout): string
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        $source = Files::read(self::LAYOUT);
        $end    = strpos($source, '</h1>');

        $this->assertIsInt($end, 'The layout has a heading.');

        $file = tempnam(sys_get_temp_dir(), 'kerr');
        file_put_contents($file, substr($source, 0, $end + 5));

        $render = function () use ($file): void {
            include $file;
        };

        ob_start();

        try {
            Closure::bind($render, $layout)();
        } finally {
            $output = (string) ob_get_clean();
            unlink($file);
        }

        return $output;
    }

    public static function messages(): array
    {
        return [
            'a script'          => ['<script>alert(1)</script>'],
            'an image'          => ['<img src=x onerror=alert(1)>'],
            'a closing heading' => ['</h1><a href="https://evil.example">Log in again</a>'],
        ];
    }

    #[DataProvider('messages')]
    public function testMessageOfTheExceptionIsText(string $message): void
    {
        $html = $this->heading((object) ['header' => '403 Forbidden', 'message' => $message]);

        $this->assertStringContainsString('403 Forbidden : ' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8'), $html);
        $this->assertSame(1, substr_count($html, '<h1>'));
        $this->assertSame(1, substr_count($html, '</h1>'));
        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('<img', $html);
        $this->assertStringNotContainsString('<a ', $html);
    }

    public function testStatusIsTextToo(): void
    {
        $html = $this->heading((object) ['header' => '404 <b>Not Found</b>', 'message' => 'The page does not exist.']);

        $this->assertStringContainsString('404 &lt;b&gt;Not Found&lt;/b&gt; : The page does not exist.', $html);
    }

    /**
     * The page of a profile which does not exist sets the status only.
     */
    public function testNoMessageGivesNoWarning(): void
    {
        $html = $this->heading((object) ['header' => '404 Not Found']);

        $this->assertStringContainsString('<h1>404 Not Found : </h1>', $html);
    }
}
