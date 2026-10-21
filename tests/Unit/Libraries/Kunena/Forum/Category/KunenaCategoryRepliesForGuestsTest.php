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

namespace Kunena\Tests\Unit\Libraries\Kunena\Forum\Category;

use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Forum\Category\KunenaCategory;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The setting of a category to hide the replies for guests, and the topic icon of solved topics.
 */
class KunenaCategoryRepliesForGuestsTest extends UnitTestCase
{
    private function category(string $params): KunenaCategory
    {
        // The parameters are the Registry of Joomla (the real one when the framework is loaded, else a small stand-in).
        return self::bare(KunenaCategory::class, ['params' => new Registry($params)]);
    }

    public static function settings(): array
    {
        return [
            'hidden in this category'     => ['{"teaser":"1"}', true],
            'shown in this category'      => ['{"teaser":"0"}', false],
            'hidden as a number'          => ['{"teaser":1}', true],
            'shown as a number'           => ['{"teaser":0}', false],
        ];
    }

    #[DataProvider('settings')]
    public function testTheSettingOfTheCategoryDecides(string $params, bool $expected): void
    {
        $this->assertSame($expected, $this->category($params)->hidesRepliesForGuests());
    }

    public function testTheSolvedIconIsTheIconOfKunena(): void
    {
        // The icon "Solved" of the topic icons of the template: the button Mark as solved sets it.
        $this->assertSame(8, KunenaTopic::ICON_SOLVED);
    }
}
