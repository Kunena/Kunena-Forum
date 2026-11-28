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

namespace Kunena\Tests\Unit\Component\Site\Controller;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The name of a member is given as text where it is escaped later: the breadcrumb of the profile, the title and the description of
 * the recent posts of a member, the last author in the feed of a category. getName() escapes it, so "Tom & Jerry" was
 * "Tom &amp; Jerry" there. (The pages need a whole site: their code is read.)
 */
class NamesEscapedOnceTest extends UnitTestCase
{
    public static function places(): array
    {
        return [
            'the breadcrumb of the profile' => [
                'src/site/src/Controller/User/Item/UserItemDisplay.php',
                "\$this->headerText = Text::sprintf('COM_KUNENA_VIEW_USER_DEFAULT', \$this->profile->getName('', false));",
            ],
            'the title of the recent posts of a member' => [
                'src/site/src/Controller/Message/Listing/Recent/MessageListingRecentDisplay.php',
                "Text::_('COM_KUNENA_FROM') . ' ' . \$user->getName('', false) . (",
            ],
            'the last author in the feed of a category' => [
                'src/site/src/Controller/Application/Category/Feed/CategoryDisplay.php',
                "KunenaFactory::getUser(\$topic->last_post_userid)->getName(\$topic->last_post_guest_name, false))",
            ],
            'the last author in the older feed view' => [
                'src/site/src/View/Category/HtmlViewFeed.php',
                "KunenaFactory::getUser(\$topic->last_post_userid)->getName(\$topic->last_post_guest_name, false))",
            ],
        ];
    }

    #[DataProvider('places')]
    public function testTheNameIsGivenAsText(string $file, string $code): void
    {
        $this->assertStringContainsString($code, Files::read($file));
    }
}
