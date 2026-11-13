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

namespace Kunena\Tests\Unit\Component\Site;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A feed asked for while the RSS feeds are disabled answered 500 instead of 404.
 */
class FeedDisabledStatusTest extends UnitTestCase
{
    public static function feedFiles(): array
    {
        return [
            'category feed controller' => ['src/site/src/Controller/Application/Category/Feed/CategoryDisplay.php'],
            'topics feed controller'   => ['src/site/src/Controller/Application/Topics/Feed/TopicsDisplay.php'],
            'category feed view'       => ['src/site/src/View/Category/HtmlViewFeed.php'],
        ];
    }

    #[DataProvider('feedFiles')]
    public function testDisabledFeedAnswersNotFound(string $file): void
    {
        $source = Files::read($file);

        $this->assertStringContainsString("Text::_('COM_KUNENA_RSS_DISABLED'), 404)", $source);
        $this->assertStringNotContainsString("Text::_('COM_KUNENA_RSS_DISABLED'), 500)", $source);
    }
}
