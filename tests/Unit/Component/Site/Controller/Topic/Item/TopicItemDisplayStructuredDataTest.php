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

namespace Kunena\Tests\Unit\Component\Site\Controller\Topic\Item;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Event\Dispatcher;
use Joomla\Input\Input;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Date\KunenaDate;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Template\KunenaTemplate;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Site\Controller\Topic\Item\TopicItemDisplay;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The structured data (JSON-LD DiscussionForumPosting) of the topic page (issue #9668): the author is the first poster, the
 * interaction type is a type of schema.org, the addresses are absolute, the values are plain text and the hidden text of the
 * first message is left out.
 */
class TopicItemDisplayStructuredDataTest extends UnitTestCase
{
    private const HOST = 'https://forum.test';

    /**
     * The values of the static properties which the test replaces, to put back afterwards.
     */
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        // The parser is in the folder External of the library.
        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        // The address of the request and of the site, an application without plugins, the configuration and the template:
        // what KunenaParser::stripBBCode() and the parser need without a site.
        $config = $this->bare(KunenaConfig::class, ['config' => ['boardTitle' => 'The Board', 'template' => 'aurelia', 'debug' => 0, 'fallbackEnglish' => 0]]);
        $this->replace(Uri::class, 'instances', ['SERVER' => new Uri(self::HOST . '/forum/topic?x=1')]);
        $this->replace(Uri::class, 'root', ['prefix' => self::HOST, 'path' => '']);
        $this->replace(Uri::class, 'base', ['prefix' => self::HOST, 'path' => '']);
        $this->replace(PluginHelper::class, 'plugins', []);
        $this->replace(KunenaConfig::class, 'instance', $config);
        $this->replace(KunenaTemplate::class, '_instances', ['aurelia' => $this->bare(KunenaTemplate::class, ['params' => new Registry(['disableEmoticons' => 1])])]);

        $language    = $this->createStub(Language::class);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getDispatcher')->willReturn(new Dispatcher());
        $application->method('getLanguage')->willReturn($language);
        $application->method('getInput')->willReturn(new Input([]));
        $application->method('isClient')->willReturn(false);
        $this->replace(Factory::class, 'application', $application);
        $this->replace(Factory::class, 'language', $language);
    }

    protected function tearDown(): void
    {
        foreach ($this->saved as [$property, $value]) {
            $property->setValue(null, $value);
        }

        $this->saved = [];
    }

    private function replace(string $class, string $name, mixed $value): void
    {
        $property      = new ReflectionProperty($class, $name);
        $this->saved[] = [$property, $property->getValue()];
        $property->setValue(null, $value);
    }

    private function user(string $name, string|false $url): KunenaUser
    {
        $user = $this->createStub(KunenaUser::class);
        $user->method('getName')->willReturn($name);
        $user->method('getURL')->willReturn($url);

        return $user;
    }

    /**
     * A topic with its first and last poster, its category and its number of replies.
     */
    private function topic(array $values = []): KunenaTopic
    {
        $values += [
            'subject' => 'A topic',
            'first'   => $this->user('First Poster', '/forum/user/5'),
            'last'    => $this->user('Last Poster', '/forum/user/9'),
            'replies' => 3,
        ];

        $category = $this->createStub(KunenaCategory::class);
        $category->method('getUrl')->willReturn('/forum/category?a=1&b=2');
        $category->name = 'News & <Views>';

        $topic = $this->createStub(KunenaTopic::class);
        $topic->method('getPermaUrl')->willReturn('/forum/category/12-a-topic?a=1&b=2');
        $topic->method('getFirstPostTime')->willReturn(new KunenaDate('2024-05-06 07:08:09'));
        $topic->method('getFirstPostAuthor')->willReturn($values['first']);
        $topic->method('getLastPostAuthor')->willReturn($values['last']);
        $topic->method('getReplies')->willReturn($values['replies']);
        $topic->method('getCategory')->willReturn($category);
        $topic->subject = $values['subject'];

        return $topic;
    }

    private function message(string $text = 'The first message', mixed $modified = null): KunenaMessage
    {
        return $this->bare(KunenaMessage::class, ['message' => $text, 'modified_time' => $modified]);
    }

    private function data(?KunenaTopic $topic = null, ?KunenaMessage $first = null, string $image = 'https://forum.test/media/kunena/email/hero-wide.png'): array
    {
        $controller = $this->createPartialMock(TopicItemDisplay::class, ['docImage']);
        $controller->expects($this->once())->method('docImage')->willReturn($image);
        $controller->topic  = $topic ?? $this->topic();
        $controller->config = KunenaConfig::getInstance();

        return $this->invoke($controller, 'getStructuredData', $first ?? $this->message());
    }

    private static function json(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP);
    }

    public function testTheTypeOfTheData(): void
    {
        $data = $this->data();

        $this->assertSame('https://schema.org', $data['@context']);
        $this->assertSame('DiscussionForumPosting', $data['@type']);
        $this->assertSame('2024-05-06T07:08:09+00:00', $data['datePublished']);
        $this->assertSame(['@type' => 'Organization', 'name' => 'The Board', 'url' => self::HOST . '/'], $data['publisher']);
    }

    public function testTheAuthorIsTheFirstPoster(): void
    {
        $data = $this->data();

        $this->assertSame(['@type' => 'Person', 'name' => 'First Poster', 'url' => self::HOST . '/forum/user/5'], $data['author']);
    }

    public function testAGuestAuthorHasNoAddress(): void
    {
        $data = $this->data($this->topic(['first' => $this->user('A guest', false)]));

        $this->assertSame(['@type' => 'Person', 'name' => 'A guest'], $data['author']);
    }

    public function testAnAbsoluteProfileAddressIsKept(): void
    {
        $data = $this->data($this->topic(['first' => $this->user('Someone', 'https://social.test/profile/5')]));

        $this->assertSame('https://social.test/profile/5', $data['author']['url']);
    }

    public function testTheRepliesAreCommentActions(): void
    {
        $data = $this->data($this->topic(['replies' => 7]));

        $this->assertSame(
            ['@type' => 'InteractionCounter', 'interactionType' => 'https://schema.org/CommentAction', 'userInteractionCount' => 7],
            $data['interactionStatistic']
        );
    }

    public function testTheHiddenTextIsLeftOut(): void
    {
        $data = $this->data(null, $this->message(
            "Visible [hide]secret-hide[/hide] [confidential]secret-conf[/confidential]\n\n [private]secret-priv[/private] end"
        ));

        $this->assertStringContainsString('Visible', $data['text']);
        $this->assertStringContainsString('end', $data['text']);
        $this->assertSame('Visible end', $data['text']);

        foreach (['secret-hide', 'secret-conf', 'secret-priv', '[', '<'] as $hidden) {
            $this->assertStringNotContainsString($hidden, $data['text']);
        }
    }

    public function testAnEmptyTextIsLeftOut(): void
    {
        $data = $this->data(null, $this->message('[hide]only hidden[/hide]'));

        $this->assertArrayNotHasKey('text', $data);
    }

    public function testTheHeadlineIsTheRawSubject(): void
    {
        $data = $this->data($this->topic(['subject' => 'A & B <i>x</i>']));

        $this->assertSame('A & B <i>x</i>', $data['headline']);
    }

    public function testTheUserTextCannotCloseTheScript(): void
    {
        $script = '</script><script>alert(1)</script>';
        $json   = self::json($this->data($this->topic(['subject' => $script]), $this->message('Look ' . $script . ' here')));

        $this->assertStringNotContainsString('<', $json);
        $this->assertStringNotContainsString('</script', $json);
        $this->assertSame($script, json_decode($json, true)['headline']);
    }

    public function testTheAddressesAreAbsolute(): void
    {
        $data = $this->data();

        $this->assertArrayNotHasKey('aggregateRating', $data);
        $this->assertSame(['@type' => 'WebPage', '@id' => $data['url']], $data['mainEntityOfPage']);
        $this->assertSame(['@type' => 'CollectionPage', 'name' => 'News & <Views>', 'url' => self::HOST . '/forum/category?a=1&b=2'], $data['isPartOf']);

        foreach ([$data['url'], $data['discussionUrl'], $data['mainEntityOfPage']['@id'], $data['isPartOf']['url']] as $url) {
            $this->assertStringStartsWith('http', $url);
            $this->assertStringNotContainsString('&amp;', $url);
        }

        $this->assertSame(self::HOST . '/forum/category/12-a-topic?a=1&b=2', $data['url']);
        $this->assertSame($data['url'], $data['discussionUrl']);
    }

    public function testNoDateModifiedWithoutAModification(): void
    {
        $this->assertArrayNotHasKey('dateModified', $this->data(null, $this->message('Text', 0)));
        $this->assertArrayNotHasKey('dateModified', $this->data(null, $this->message('Text', null)));
    }

    public function testTheDateModifiedOfTheFirstMessage(): void
    {
        $data = $this->data(null, $this->message('Text', strtotime('2025-01-02 03:04:05 UTC')));

        $this->assertSame('2025-01-02T03:04:05+00:00', $data['dateModified']);
    }

    public function testAnEmptyImageIsLeftOut(): void
    {
        $this->assertArrayNotHasKey('image', $this->data(null, null, ''));
        $this->assertSame('https://forum.test/a.png', $this->data(null, null, 'https://forum.test/a.png')['image']);
    }
}
