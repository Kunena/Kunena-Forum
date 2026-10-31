<?php

/**
 * Kunena Component
 *
 * Regression test for the HTML emails (report to the moderators, new message to the subscribers, message to the moderators). They
 * printed the subject of the topic, the names, the category and the text of the report as they were typed. The subject is saved as
 * typed (the pages escape it when they show it), so a member could put a link or an image in a mail which arrives from the address
 * of the forum. The values are now escaped where they enter the HTML, in the template aurelia and in the template system.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/fixtures/generic_config.php';

class EmailLayoutEscapingTest extends TestCase
{
    private const TEMPLATES = __DIR__ . '/../../../src/site/template';

    private const EVIL_LINK   = '<a href="https://evil.example/login">Verify your account</a>';
    private const EVIL_IMAGE  = '<img src="https://evil.example/pixel.gif">';
    private const EVIL_SCRIPT = '<script>alert(1)</script>';

    protected function setUp(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        // The stand-ins of Text and Uri.
        require_once __DIR__ . '/fixtures/quote_stubs.php';

        // The mails to the subscribers carry the text of the message when the forum is set to do so.
        \Kunena\Tests\Unit\BBCode\Fixtures\GenericConfig::$values['mailFull'] = 1;

        if (!class_exists('Kunena\Forum\Libraries\Config\KunenaConfig', false)) {
            class_alias(\Kunena\Tests\Unit\BBCode\Fixtures\GenericConfig::class, 'Kunena\Forum\Libraries\Config\KunenaConfig');
        }
    }

    public static function layouts(): array
    {
        $cases = [];

        foreach (['aurelia', 'system'] as $template) {
            foreach (['report/default', 'subscription/default', 'subscription/moderator'] as $layout) {
                $cases["$template $layout"] = [$template, $layout];
            }
        }

        return $cases;
    }

    private function message(string $subject, string $author, string $category): object
    {
        $user  = new class ($author) {
            public $username = '';
            public $name     = '';

            public function __construct(private string $author)
            {
                $this->username = $author;
                $this->name     = $author;
            }

            public function getName($default = '', $escape = true)
            {
                return $this->author;
            }
        };
        $topic = new class ($subject) {
            public function __construct(public string $subject)
            {
            }
        };
        $cat   = new class ($category) {
            public function __construct(public string $name)
            {
            }
        };

        return new class ($subject, $user, $topic, $cat) {
            public $parent        = 1;
            public $modified_time = null;
            public $modified_by   = null;
            public $hold          = 0;

            public function __construct(public string $subject, private object $user, private object $topic, private object $cat)
            {
            }

            public function getAuthor()
            {
                return $this->user;
            }

            public function getTopic()
            {
                return $this->topic;
            }

            public function getCategory()
            {
                return $this->cat;
            }

            public function getUrl($category = null, $xhtml = true)
            {
                return '/forum/1-general/7-hello?mesid=5';
            }

            public function displayField($name, $html = true, $context = '')
            {
                return '<p>THE MESSAGE</p>';
            }

            public function __get($name)
            {
                return null;
            }
        };
    }

    private function render(string $template, string $layout, object $message, array $props = []): string
    {
        $view = new #[\AllowDynamicProperties] class () {
            public $approved = false;
            public $once     = false;
            public $title    = '';
            public $content  = '';
            public $mail;
            public $me;
            public $message;

            public function text($key)
            {
                return $key;
            }

            public function escape($text)
            {
                return htmlspecialchars((string) $text, ENT_COMPAT, 'UTF-8');
            }

            public function __call($name, $arguments)
            {
                return '';
            }
        };

        $view->mail = new #[\AllowDynamicProperties] class () {
            public function __call($name, $arguments)
            {
                return null;
            }
        };

        $view->me      = (object) ['username' => 'reporter', 'name' => 'Reporter'];
        $view->message = $message;

        foreach ($props as $name => $value) {
            $view->$name = $value;
        }

        $file = self::TEMPLATES . "/$template/layouts/email/$layout.php";
        $this->assertFileExists($file);

        ob_start();
        (function () use ($file) {
            include $file;
        })->call($view);

        return ob_get_clean();
    }

    #[DataProvider('layouts')]
    public function testSubjectIsText(string $template, string $layout): void
    {
        foreach ([self::EVIL_LINK, self::EVIL_IMAGE, self::EVIL_SCRIPT] as $evil) {
            $html = $this->render($template, $layout, $this->message('Hi ' . $evil, 'Visitor', 'General'));

            $this->assertStringContainsString('THE MESSAGE', $html, 'The layout was rendered.');
            $this->assertStringNotContainsString($evil, $html, "$template $layout prints the subject as markup");
            $this->assertSame('', $this->markup($html), "$template $layout lets the subject add a tag");
            $this->assertStringContainsString(htmlspecialchars($evil, ENT_QUOTES, 'UTF-8'), $html, 'It is shown as text.');
        }
    }

    /**
     * The tags of the layout itself leave out; what is left is what the member could add.
     */
    private function markup(string $html): string
    {
        return preg_match('#<(a|img|script|iframe|form)\b[^>]*evil\.example[^>]*>#i', $html, $m) ? $m[0] : '';
    }

    #[DataProvider('layouts')]
    public function testNamesAndCategoryAreText(string $template, string $layout): void
    {
        $html = $this->render($template, $layout, $this->message('Hello', self::EVIL_LINK, self::EVIL_IMAGE));

        $this->assertSame('', $this->markup($html), "$template $layout lets a name or a category add markup");
        $this->assertStringNotContainsString(self::EVIL_LINK, $html);
        $this->assertStringNotContainsString(self::EVIL_IMAGE, $html);
    }

    public static function reportLayouts(): array
    {
        return [['aurelia'], ['system']];
    }

    #[DataProvider('reportLayouts')]
    public function testReportTextAndReporterAreText(string $template): void
    {
        $html = $this->render(
            $template,
            'report/default',
            $this->message('Hello', 'Visitor', 'General'),
            ['title' => self::EVIL_LINK, 'content' => self::EVIL_IMAGE . self::EVIL_SCRIPT, 'me' => (object) ['username' => self::EVIL_LINK, 'name' => self::EVIL_IMAGE]]
        );

        $this->assertSame('', $this->markup($html));
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;a href=&quot;https://evil.example/login&quot;&gt;', $html);
    }

    #[DataProvider('layouts')]
    public function testQuoteInASubjectCannotBreakAnAttribute(string $template, string $layout): void
    {
        $html = $this->render($template, $layout, $this->message('"><img src=x onerror=alert(1)>', 'a"b', "c'd"));

        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringNotContainsString('onerror=alert(1)>', str_replace('&gt;', '', $html));
    }

    #[DataProvider('layouts')]
    public function testTheMessageItselfIsStillShown(string $template, string $layout): void
    {
        $html = $this->render($template, $layout, $this->message('Plain & simple', 'Jane Doe', 'General'));

        $this->assertStringContainsString('<p>THE MESSAGE</p>', $html, 'The text of the message is rendered by the parser and is html.');
        $this->assertStringContainsString('Plain &amp; simple', $html);
        $this->assertStringContainsString('Jane Doe', $html);
        $this->assertStringContainsString('https://forum.example/forum/1-general/7-hello?mesid=5', $html);
    }

    public function testPlainTextBranchIsNotEscaped(): void
    {
        // The plain text mail is not html: "&amp;" would be seen as text.
        $source = file_get_contents(self::TEMPLATES . '/aurelia/layouts/email/subscription/default.php');

        $this->assertStringContainsString('{$subject}', $source);
        $this->assertStringNotContainsString('htmlspecialchars((string) $subject', substr($source, strpos($source, '<<<EOS')));
    }
}
