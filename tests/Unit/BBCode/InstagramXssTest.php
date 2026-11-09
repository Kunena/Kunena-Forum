<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\BBCode;

use Kunena\Forum\Libraries\BBCode\KunenaBBCode;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Nbbc\BBCode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class InstagramUriHelperStub
{
    public static function parse_url(string $url)
    {
        return parse_url($url);
    }
}

class InstagramXssTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', realpath(__DIR__ . '/../../../src/libraries/kunena'));
        }

        if (!class_exists('Joomla\Uri\UriHelper', false)) {
            class_alias(InstagramUriHelperStub::class, 'Joomla\Uri\UriHelper');
        }

        require_once KPATH_FRAMEWORK . '/src/BBCode/KunenaBBCode.php';
    }

    public static function validInputs(): array
    {
        return [
            'post ID'            => ['Ab_c-123', 'https://www.instagram.com/p/Ab_c-123/embed/'],
            'post URL'           => ['https://www.instagram.com/p/Ab_c-123/', 'https://www.instagram.com/p/Ab_c-123/embed/'],
            'reel with query'    => ['https://instagram.com/reel/Ab_c/?utm_source=ig_web_copy_link#fragment', 'https://instagram.com/reel/Ab_c/embed/'],
            'short domain'       => ['http://instagr.am/p/Ab_c', 'https://instagr.am/p/Ab_c/embed/'],
            'no scheme'          => ['www.instagram.com/p/Ab_c/', 'https://www.instagram.com/p/Ab_c/embed/'],
            'case and username'  => ['HTTPS://WWW.INSTAGRAM.COM/user.name/', 'https://www.instagram.com/user.name/embed/'],
        ];
    }

    #[DataProvider('validInputs')]
    public function testValidInputsAreEmbedded(string $input, string $url): void
    {
        $html = KunenaBBCodeLibrary::DoInstagram(null, '', '', '', '', $input);

        $this->assertSame('<div class="embed-container"><iframe src="' . $url . '" style="border: 0"></iframe></div>', $html);
    }

    public static function invalidInputs(): array
    {
        return [
            'autolink payload'       => ['https://instagram.com/p/x"onload=alert(document.cookie)//'],
            'post ID payload'        => ['x" onload="alert(1)'],
            'markup'                 => ['<img src=x onerror=alert(1)>'],
            'lookalike domain'       => ['https://instagram.com.evil.example/p/x'],
            'prefixed domain'        => ['https://evilinstagram.com/p/x'],
            'unescaped dots'         => ['https://instagramXcom/p/x'],
            'userinfo'               => ['https://instagram.com@evil.example/p/x'],
            'unsupported scheme'     => ['javascript://instagram.com/p/x'],
            'protocol relative'      => ['//instagram.com/p/x'],
            'port'                   => ['https://instagram.com:443/p/x'],
            'path traversal'         => ['https://instagram.com/p/../x'],
            'encoded path'           => ['https://instagram.com/p/%22onload=alert(1)'],
            'line break'             => ["https://instagram.com/p/x\nonload=alert(1)"],
            'malformed URL'          => ['https://'],
        ];
    }

    #[DataProvider('invalidInputs')]
    public function testInvalidInputsAreDisplayedAsEscapedText(string $input): void
    {
        $html = KunenaBBCodeLibrary::renderInstagram(null, $input);

        $this->assertSame(htmlspecialchars(trim($input), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), $html);
        $this->assertStringNotContainsString('<iframe', $html);
    }

    public function testMinimalModeKeepsSafeIdLink(): void
    {
        $bbcode = (object) ['parent' => (object) ['forceMinimal' => true]];

        $this->assertSame(
            '<a href="Ab_c-123" rel="nofollow noopener noreferrer" target="_blank">Ab_c-123</a>',
            KunenaBBCodeLibrary::renderInstagram($bbcode, 'Ab_c-123')
        );
        $this->assertSame('javascript:alert(1)', KunenaBBCodeLibrary::renderInstagram($bbcode, 'javascript:alert(1)'));
    }

    private function parser(): KunenaBBCode
    {
        $parser  = (new \ReflectionClass(KunenaBBCode::class))->newInstanceWithoutConstructor();
        $library = (new \ReflectionClass(KunenaBBCodeLibrary::class))->newInstanceWithoutConstructor();
        (new \ReflectionMethod(BBCode::class, '__construct'))->invoke($parser, $library);
        $parser->config = (object) [
            'autoLink'            => false,
            'autoEmbedSoundcloud' => false,
            'autoEmbedInstagram'  => true,
            'autoEmbedYoutube'    => false,
            'autoEmbedEbay'       => false,
        ];
        $parser->setEnableSmileys(false);
        $parser->setDetectURLs(true);
        $parser->setURLPattern([$parser, 'parseUrl']);

        return $parser;
    }

    public function testAutolinkPayloadCannotCreateAnIframeAttribute(): void
    {
        $input = 'https://instagram.com/p/x"onload=alert(document.cookie)//';
        $html  = $this->parser()->parse($input);

        $this->assertStringNotContainsString('<iframe', $html);
        $this->assertStringNotContainsString('"onload=', $html);
        $this->assertStringContainsString('&quot;onload=', $html);
    }

    public function testAutolinkEmbedsInstagramButNotLookalikeHosts(): void
    {
        $parser = $this->parser();

        $this->assertStringContainsString('src="https://instagram.com/p/abc/embed/"', $parser->parse('https://instagram.com/p/abc'));
        $this->assertStringContainsString('src="https://instagr.am/p/abc/embed/"', $parser->parse('https://instagr.am/p/abc'));
        $this->assertStringNotContainsString('<iframe', $parser->parse('https://instagram.com.evil.example/p/abc'));
    }

    public function testInstagramTagEmbedsAnIdAndRejectsAnAttributePayload(): void
    {
        $parser = $this->parser();

        $this->assertStringContainsString('src="https://www.instagram.com/p/abc/embed/"', $parser->parse('[instagram]abc[/instagram]'));
        $html = $parser->parse('[instagram]x"onload=alert(1)[/instagram]');

        $this->assertStringNotContainsString('<iframe', $html);
        $this->assertStringNotContainsString('"onload=', $html);
    }
}
