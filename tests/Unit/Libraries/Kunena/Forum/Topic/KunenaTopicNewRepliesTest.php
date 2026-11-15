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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Topic;

use Kunena\Forum\Libraries\Forum\Topic\KunenaTopicNewReplies;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The new replies of the topic which is read: the time between two questions, the task which answers them (a token, a topic which
 * the user may read, the option of the template) and the topic page which asks them.
 */
class KunenaTopicNewRepliesTest extends UnitTestCase
{
    public static function intervals(): array
    {
        return [
            'the setting'          => [45, 45],
            'as text'              => ['60', 60],
            'too short'            => [3, KunenaTopicNewReplies::MIN_INTERVAL],
            'zero is the default'  => [0, 30],
            'negative'             => [-5, 30],
            'not a number'         => ['soon', 30],
            'nothing'              => [null, 30],
        ];
    }

    #[DataProvider('intervals')]
    public function testTheTimeBetweenTwoQuestions(mixed $setting, int $seconds): void
    {
        $this->assertSame($seconds, KunenaTopicNewReplies::interval($setting));
    }

    public function testTheTaskChecksTheTokenTheReadingAndTheOptionBeforeItAnswers(): void
    {
        $source = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertSame(1, preg_match('/public function newreplies\(\)\s*\{(.*?)\n    \}\n/s', $source, $m));

        $find = strpos($m[1], 'KunenaTopicNewReplies::find(');

        $this->assertIsInt($find);
        $this->assertLessThan($find, strpos($m[1], "Session::checkToken('get')"));
        $this->assertLessThan($find, strpos($m[1], "\$topic->isAuthorised('read')"));
        $this->assertLessThan($find, strpos($m[1], "params->get('ajaxTopicList', 1)"));
        $this->assertStringContainsString('(int) $this->me->userid', $m[1], 'The own replies of the user are not left out.');
    }

    public function testTheTopicPageAsksWithTheOptionOfTheBackgroundReload(): void
    {
        $source = Files::read('src/site/template/aurelia/layouts/topic/item/default.php');

        $this->assertMatchesRegularExpression("/if \(\\\$this->ktemplate->params->get\('ajaxTopicList', 1\)\) \{.*?task=newreplies.*?Session::getFormToken\(\).*?\\\$this->addScript\('assets\/js\/topicnew\.js'\);/s", $source);
        $this->assertStringContainsString("'&after=' . (int) \$this->topic->last_post_id", $source);
        $this->assertFileExists(Files::path('src/site/template/aurelia/assets/js/topicnew.js'));
    }
}
