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

namespace Kunena\Tests\Integration\Plugin\Kunena\Reports;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Event\GenericEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Uri\Uri;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicHelper;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Plugin\Kunena\Reports\Extension\Reports;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;
use ReflectionClass;
use ReflectionProperty;

/**
 * The plugin Kunena - Reports, which makes a topic of the reports of a post in a category of the moderators.
 *
 * The topic of the reports of a post: a moderator who handled the reports deletes it (it goes to the trash). A new report of the same
 * post was added as a reply to that deleted topic, where no moderator sees it; it starts a new topic instead.
 */
class ReportsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private const REPORTS = 3;

    private const REPORT_TOPIC = 5;

    private array $saved = [];

    /**
     * @var  string[]  What the plugin did: "reply" or "topic".
     */
    private array $done = [];

    /**
     * @var  array  The subject and the text of the new topic of a report.
     */
    private array $fields = [];

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $root = \dirname(__DIR__, 5);
        require_once $root . '/src/plugins/plg_kunena_reports/src/Extension/Reports.php';

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        // The texts are their keys (with the placeholders of two of them); the language of the plugin is loaded already.
        $language = $this->createStub(Language::class);
        $language->method('_')->willReturnCallback(static fn ($key) => ['PLG_KUNENA_REPORTS_SUBJECT' => 'Report by %s: %s',
            'PLG_KUNENA_REPORTS_POST_BY' => 'by %s'][$key] ?? $key);
        $language->method('getPaths')->willReturn(['loaded' => true]);
        $application = $this->createStub(CMSApplicationInterface::class);
        $application->method('getLanguage')->willReturn($language);

        // The category of the reports and the deleted topic of the reports of the post.
        $category = $this->createStub(KunenaCategory::class);
        $category->method('exists')->willReturn(true);
        $category->method('newTopic')->willReturnCallback(function ($fields) {
            $this->done[]  = 'topic';
            $this->fields = $fields;
            $topic        = $this->createStub(KunenaTopic::class);
            $topic->id    = 6;

            return [$topic, $this->createStub(KunenaMessage::class)];
        });

        $reportTopic       = $this->createStub(KunenaTopic::class);
        $reportTopic->id   = self::REPORT_TOPIC;
        $reportTopic->hold = 2;
        $reportTopic->method('exists')->willReturn(true);
        $reportTopic->method('newReply')->willReturnCallback(function () {
            $this->done[] = 'reply';

            return $this->createStub(KunenaMessage::class);
        });

        $this->saved = [
            'container'   => Factory::$container,
            'application' => Factory::$application,
            'language'    => Factory::$language,
            'categories'  => $this->replace(KunenaCategoryHelper::class, '_instances', [self::REPORTS => $category]),
            'topics'      => $this->replace(KunenaTopicHelper::class, '_instances', [self::REPORT_TOPIC => $reportTopic]),
            'uri'         => $this->replace(Uri::class, 'instances', ['SERVER' => new Uri('http://localhost/index.php')]),
        ];

        Factory::$container   = $container;
        Factory::$application = $application;
        Factory::$language    = $language;

        $driver->setQuery(str_replace(';', '', (string) file_get_contents($root . '/src/plugins/plg_kunena_reports/sql/install.mysql.utf8.sql')))->execute();
        // The table of the plugin is dropped after the test: its row is not one which the helper of the tests empties.
        $driver->setQuery('INSERT INTO #__kunena_reports (message_id, topic_id, reports, reporters, created_time) VALUES (10, ' . self::REPORT_TOPIC . ", 1, '[7]', 1)")->execute();
    }

    protected function tearDown(): void
    {
        if ($this->saved) {
            $this->getDBDriver()->setQuery('DROP TABLE IF EXISTS `#__kunena_reports`')->execute();
            Factory::$container   = $this->saved['container'];
            Factory::$application = $this->saved['application'];
            Factory::$language    = $this->saved['language'];
            $this->replace(KunenaCategoryHelper::class, '_instances', $this->saved['categories']);
            $this->replace(KunenaTopicHelper::class, '_instances', $this->saved['topics']);
            $this->replace(Uri::class, 'instances', $this->saved['uri']);
        }

        parent::tearDown();
    }

    public function testANewReportOfThePostStartsANewTopicWhenTheTopicOfItsReportsWasDeleted(): void
    {
        $this->report(10, 'Author', 'Reporter');

        $this->assertSame(['topic'], $this->done);
        $this->assertSame([['topic_id' => 6]], $this->fetchAll('SELECT topic_id FROM #__kunena_reports WHERE message_id = 10'));
    }

    /**
     * The names in the topic of a report are text in BBCode (the parser escapes them when the topic is shown): they were
     * HTML-escaped already, and "Tom & Jerry" read "Tom &amp; Jerry" in the subject and in the text of the report.
     */
    public function testTheNamesInTheReportAreText(): void
    {
        $this->report(11, 'Ann & Bob', 'Tom & Jerry');

        $this->assertSame(['topic'], $this->done);
        $this->assertStringContainsString('Tom & Jerry', $this->fields['subject']);
        $this->assertStringContainsString(']Tom & Jerry[/url]', $this->fields['message']);
        $this->assertStringContainsString('[quote="Ann & Bob post=11', $this->fields['message']);
        $this->assertStringNotContainsString('&amp;', $this->fields['subject'] . $this->fields['message']);
    }

    /**
     * The plugin gets the report of the message $id (in the category 2) of $authorName by $reporterName. The names are given by
     * getName() as the forum does: HTML-escaped unless asked for text.
     */
    private function report(int $id, string $authorName, string $reporterName): void
    {
        $plugin = (new ReflectionClass(Reports::class))->newInstanceWithoutConstructor();
        (new ReflectionProperty(Reports::class, 'params'))->setValue($plugin, new Registry(['category' => self::REPORTS]));

        $name = static fn (string $name) => static fn ($visitorname = '', $escape = true) => $escape ? htmlspecialchars($name, ENT_COMPAT, 'UTF-8') : $name;

        $author = $this->createStub(KunenaUser::class);
        $author->method('getName')->willReturnCallback($name($authorName));

        $message          = $this->createStub(KunenaMessage::class);
        $message->id      = $id;
        $message->catid   = 2;
        $message->userid  = 42;
        $message->name    = $authorName;
        $message->hold    = 0;
        $message->message = 'The post';
        $message->method('exists')->willReturn(true);
        $message->method('getAuthor')->willReturn($author);

        $topic                = $this->createStub(KunenaTopic::class);
        $topic->subject       = 'Topic';
        $topic->first_post_id = 9;

        $reporter         = $this->createStub(KunenaUser::class);
        $reporter->userid = 8;
        $reporter->method('exists')->willReturn(true);
        $reporter->method('getName')->willReturnCallback($name($reporterName));
        $reporter->method('getURL')->willReturn('/profile/8');

        $event = new GenericEvent('onKunenaReportMessage', ['message' => $message, 'topic' => $topic, 'reporter' => $reporter,
            'reason' => 'Spam', 'text' => '', 'url' => 'http://localhost/post/' . $id]);
        $plugin->onKunenaReportMessage($event);
    }

    /**
     * Set a static property of a class and give back the value it had.
     */
    private function replace(string $class, string $name, mixed $value): mixed
    {
        $property = new ReflectionProperty($class, $name);
        $old      = $property->getValue();
        $property->setValue(null, $value);

        return $old;
    }
}
