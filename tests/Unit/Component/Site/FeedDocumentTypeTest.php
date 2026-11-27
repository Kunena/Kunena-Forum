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

class FeedDocumentTypeTest extends UnitTestCase
{
    public static function feedControllers(): array
    {
        return [
            'category feed controller' => ['src/site/src/Controller/Application/Category/Feed/CategoryDisplay.php'],
            'topics feed controller'   => ['src/site/src/Controller/Application/Topics/Feed/TopicsDisplay.php'],
        ];
    }

    #[DataProvider('feedControllers')]
    public function testNonFeedDocumentIsRejectedBeforeAddingFeedItems(string $file): void
    {
        $source = Files::read($file);
        $typeCheck = strpos($source, '$this->document instanceof FeedDocument');
        $rejection = strpos($source, "throw new KunenaException(Text::_('COM_KUNENA_NO_ACCESS'), 404)");
        $addItem = strpos($source, '$this->document->addItem($item)');

        $this->assertNotFalse($typeCheck);
        $this->assertNotFalse($rejection);
        $this->assertNotFalse($addItem);
        $this->assertLessThan($addItem, $typeCheck);
        $this->assertLessThan($addItem, $rejection);
    }
}
