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

use PHPUnit\Framework\TestCase;

class FeedLanguageTest extends TestCase
{
    public function testFeedLoadsViewLanguageBeforeEitherControllerPath(): void
    {
        $source = file_get_contents(__DIR__ . '/../../../../src/site/src/Dispatcher/Dispatcher.php');

        $this->assertMatchesRegularExpression(
            "/if \\(\\\$app->getDocument\\(\\)->getType\\(\\) === 'feed'\\) \\{\\s*KunenaFactory::loadLanguage\\('com_kunena\\.views'\\);\\s*\\}/",
            $source
        );
        $this->assertLessThan(
            strpos($source, 'KunenaControllerApplication::getInstance('),
            strpos($source, "KunenaFactory::loadLanguage('com_kunena.views')")
        );
        $this->assertLessThan(
            strpos($source, 'KunenaController::getInstance()'),
            strpos($source, "KunenaFactory::loadLanguage('com_kunena.views')")
        );
    }
}
