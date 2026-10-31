<?php

/**
 * Kunena Component
 *
 * Regression test for the topic edit form.
 *
 *  1. The reason for the edit, and the poll title and options, were printed into value="..." without escaping.
 *  2. TopicController::edit() put the submitted values in the session when the CSRF token was wrong. That is every
 *     request from another site, so it could fill the form of the user with its own values (and with 1. run a script).
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\Topic;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EditFormTextStub
{
    public static function _($text)
    {
        return (string) $text;
    }
}

class EditFormPrefillTest extends TestCase
{
    private const LAYOUT     = __DIR__ . '/../../../src/site/template/aurelia/layouts/topic/edit/default.php';
    private const CONTROLLER = __DIR__ . '/../../../src/site/src/Controllers/TopicController.php';

    public static function setUpBeforeClass(): void
    {
        if (!class_exists('Joomla\CMS\Language\Text', false)) {
            class_alias(EditFormTextStub::class, 'Joomla\CMS\Language\Text');
        }
    }

    /**
     * Render one tag of the real layout with the given object as $this.
     */
    private function renderTag(string $pattern, object $view): string
    {
        $source = file_get_contents(self::LAYOUT);

        $this->assertSame(1, preg_match($pattern, $source, $m), 'Tag not found in the layout: ' . $pattern);

        $tag = str_replace('Text::_(', '\\Joomla\\CMS\\Language\\Text::_(', $m[0]);
        $tmp = tempnam(sys_get_temp_dir(), 'ktag');
        file_put_contents($tmp, $tag);

        try {
            ob_start();
            (function () use ($tmp) {
                include $tmp;
            })->call($view);

            return ob_get_clean();
        } finally {
            @unlink($tmp);
        }
    }

    private function view(array $props): object
    {
        $view = new #[\AllowDynamicProperties] class () {
            public function escape($text)
            {
                return htmlspecialchars((string) $text, ENT_COMPAT, 'UTF-8');
            }
        };

        foreach ($props as $name => $value) {
            $view->$name = $value;
        }

        return $view;
    }

    public static function payloads(): array
    {
        return [
            'autofocus handler' => ['" autofocus onfocus="alert(document.domain)'],
            'close the tag'     => ['"><img src=x onerror=alert(1)>'],
            'single quote'      => ["' onmouseover='alert(1)"],
            'ampersand'         => ['a&b<c>d'],
        ];
    }

    /**
     * Parse what the layout printed, the way a browser would: one input, only the attributes of the layout, the
     * payload as the text of the value, and nothing else (no new tag, no event handler).
     */
    private function assertInert(string $html, string $payload): void
    {
        $previous = libxml_use_internal_errors(true);
        $dom      = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $inputs = $dom->getElementsByTagName('input');

        $this->assertSame(1, $inputs->length, 'The payload must not create another tag: ' . $html);
        $this->assertSame(0, $dom->getElementsByTagName('img')->length);
        $this->assertSame(0, $dom->getElementsByTagName('script')->length);

        $input = $inputs->item(0);

        foreach ($input->attributes as $attribute) {
            $this->assertStringStartsNotWith('on', strtolower($attribute->name), 'An event handler was added: ' . $attribute->name);
            $this->assertNotSame('autofocus', strtolower($attribute->name), 'An attribute was added: ' . $attribute->name);
        }

        $this->assertSame($payload, $input->getAttribute('value'), 'The value must come back exactly as it was sent.');
    }

    #[DataProvider('payloads')]
    public function testReasonForTheEditIsEscaped(string $payload): void
    {
        $html = $this->renderTag('/<input class="form-control" name="modified_reason".*?\/>/s', $this->view(['modified_reason' => $payload]));

        $this->assertInert($html, $payload);

        // Still exactly one input, with the attributes of the layout.
        $this->assertSame(1, substr_count($html, '<input'));
        $this->assertStringContainsString('name="modified_reason"', $html);
        $this->assertStringContainsString('maxlength="200"', $html);
    }

    public function testNormalReasonIsShownAsItWas(): void
    {
        $html = $this->renderTag('/<input class="form-control" name="modified_reason".*?\/>/s', $this->view(['modified_reason' => 'Fixed a typo']));

        $this->assertStringContainsString('value="Fixed a typo"', $html);
    }

    #[DataProvider('payloads')]
    public function testPollTitleIsEscaped(string $payload): void
    {
        $poll = (object) ['id' => 3, 'title' => $payload, 'polltimetolive' => '2030-01-01 00:00:00'];
        $view = $this->view(['message' => new class ($poll) {
            public function __construct(private object $poll)
            {
            }

            public function getTopic()
            {
                $poll = $this->poll;

                return new class ($poll) {
                    public function __construct(private object $poll)
                    {
                    }

                    public function getPoll()
                    {
                        return $this->poll;
                    }
                };
            }
        }]);

        $html = $this->renderTag('/<input type="hidden" id="ckeditor_dialog_polltitle".*?\/>/s', $view);

        $this->assertInert($html, $payload);
        $this->assertSame(1, substr_count($html, '<input'));
    }

    #[DataProvider('payloads')]
    public function testPollOptionIsEscaped(string $payload): void
    {
        $view = $this->view([]);
        $tag  = $this->renderTagWithOption($payload, $view);

        $this->assertInert($tag, $payload);
        $this->assertSame(1, substr_count($tag, '<input'));
    }

    private function renderTagWithOption(string $payload, object $view): string
    {
        $source = file_get_contents(self::LAYOUT);

        $this->assertSame(1, preg_match('/<input type="hidden" class="ckeditor_dialog_polloption".*?\/>/s', $source, $m));

        $tmp = tempnam(sys_get_temp_dir(), 'ktag');
        file_put_contents($tmp, '<?php $index = 0; $option = (object) ["text" => $payloadText]; ?>' . $m[0]);

        try {
            ob_start();
            (function () use ($tmp, $payload) {
                $payloadText = $payload;
                include $tmp;
            })->call($view);

            return ob_get_clean();
        } finally {
            @unlink($tmp);
        }
    }

    public function testNoUnescapedValueOfTheFormIsLeft(): void
    {
        $source = file_get_contents(self::LAYOUT);

        // value="<?php echo $x ...": every dynamic value must go through escape(), intval(), Text or a route.
        preg_match_all('/value="<\?php echo ([^?]*?)\s*;?\s*\?>"/', $source, $m);

        $bad = [];

        foreach ($m[1] as $expression) {
            if (!preg_match('/escape\(|\\\\intval\(|intval\(|Text::_\(|KunenaRoute::_\(|implode\(|array_keys\(/', $expression)) {
                $bad[] = trim($expression);
            }
        }

        // Two values are not typed by a member: the category id (a number from the table) and the id of a topic icon,
        // which comes from the icon set files that the administrator installs.
        $bad = array_values(array_diff($bad, ['$this->message->catid', '$icon->id']));

        $this->assertSame([], $bad, "Printed into value=\"...\" without escaping:\n" . implode("\n", $bad));
    }

    // The controller.

    private function edit(): string
    {
        $source = file_get_contents(self::CONTROLLER);

        $this->assertSame(1, preg_match('/public function edit\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        return $m[1];
    }

    public function testNothingIsKeptWhenTheTokenIsWrong(): void
    {
        $body = $this->edit();

        $this->assertSame(1, preg_match('/if \(!Session::checkToken\(\'post\'\)\) \{(.*?)return;/s', $body, $m), 'token check not found');
        $this->assertStringNotContainsString('postfields', $m[1], 'A request with a wrong token must not store its values in the session.');
        $this->assertStringContainsString('COM_KUNENA_ERROR_TOKEN', $m[1]);
    }

    public function testValuesAreOnlyKeptAfterTheTokenWasChecked(): void
    {
        $body = $this->edit();

        $token = strpos($body, "Session::checkToken('post')");
        $this->assertNotFalse($token);

        preg_match_all("/setUserState\\('com_kunena\\.postfields', \\\$fields\\)/", $body, $stores, PREG_OFFSET_CAPTURE);

        $this->assertNotEmpty($stores[0], 'The values are still kept for a refused or failed edit.');

        foreach ($stores[0] as [, $offset]) {
            $this->assertGreaterThan($token, $offset, 'The session is only written after the token was checked.');
        }
    }

    public function testNoActionStoresValuesBeforeItsTokenCheck(): void
    {
        $source = file_get_contents(self::CONTROLLER);

        foreach (preg_split('/\n    (?=public function |protected function )/', $source) as $method) {
            if (!preg_match("/setUserState\\('com_kunena\\.postfields', \\\$fields\\)/", $method, $store, PREG_OFFSET_CAPTURE)) {
                continue;
            }

            preg_match('/function (\w+)/', $method, $name);

            $token = strpos($method, 'Session::checkToken(');

            $this->assertNotFalse($token, ($name[1] ?? '?') . ' stores values but never checks a token');
            $this->assertGreaterThan($token, $store[0][1], ($name[1] ?? '?') . ' stores values before the token check');
        }
    }
}
