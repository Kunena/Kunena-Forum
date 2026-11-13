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

namespace Kunena\Tests\Unit\Component\Site\Controller\Category\Topics;

use Kunena\Forum\Site\Controller\Category\Topics\CategoryTopicsDisplay;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The meta description of a category page comes from the category itself before the description of a menu item of another
 * page, such as the "Forum" menu item (issue #7475).
 */
class CategoryTopicsDisplayTest extends UnitTestCase
{
    private const FORUM = ['option' => 'com_kunena', 'view' => 'home'];

    private const CATEGORY = ['option' => 'com_kunena', 'view' => 'category', 'catid' => '5'];

    private const DESCRIPTION = 'We encourage new members to introduce themselves here. Get to know one another and share your interests.';

    private const GENERATED = 'Parent - Category - Board';

    protected function setUp(): void
    {
        $this->requireJoomla();
    }

    private function description(array $menuQuery, string $menuDescription, string $categoryDescription, string $generatedText = self::GENERATED, string $pagesText = '', int $categoryId = 5): string
    {
        return $this->invoke(CategoryTopicsDisplay::class, 'getMetaDescription', $menuQuery, $menuDescription, $categoryId, $categoryDescription, $generatedText, $pagesText);
    }

    public function testTheCategoryDescriptionComesBeforeTheForumMenuItem(): void
    {
        $this->assertSame(self::DESCRIPTION, $this->description(self::FORUM, 'Test Description', self::DESCRIPTION));
    }

    public function testAMenuItemOfThisCategoryComesFirst(): void
    {
        $this->assertSame('Own text', $this->description(self::CATEGORY, 'Own text', self::DESCRIPTION));
    }

    public function testTheDefaultLayoutIsTheMenuItemOfThisCategory(): void
    {
        $this->assertSame('Own text', $this->description(self::CATEGORY + ['layout' => 'default'], 'Own text', self::DESCRIPTION));
    }

    public function testTheListLayoutIsNotTheMenuItemOfThisCategory(): void
    {
        $this->assertSame(self::DESCRIPTION, $this->description(self::CATEGORY + ['layout' => 'list'], 'Own text', self::DESCRIPTION));
    }

    public function testAMenuItemOfAnotherCategoryIsNotUsedFirst(): void
    {
        $this->assertSame(self::DESCRIPTION, $this->description(['catid' => '6'] + self::CATEGORY, 'Own text', self::DESCRIPTION));
    }

    public function testAMenuItemOfAnotherComponentIsNotUsedFirst(): void
    {
        $query = ['option' => 'com_content', 'view' => 'category', 'catid' => '5'];

        $this->assertSame(self::DESCRIPTION, $this->description($query, 'Own text', self::DESCRIPTION));
    }

    public function testTheMenuDescriptionWithoutACategoryDescription(): void
    {
        $this->assertSame('Test Description', $this->description(self::FORUM, 'Test Description', ''));
        $this->assertSame('Test Description', $this->description(self::FORUM, 'Test Description', " \n\t "));
        $this->assertSame('Test Description', $this->description(self::FORUM, 'Test Description', '<p> </p>'));
    }

    public function testTheGeneratedTextWithoutAnyDescription(): void
    {
        $this->assertSame(self::GENERATED, $this->description([], '', ''));
    }

    public function testTheCategoryDescriptionWithoutAMenuItem(): void
    {
        $this->assertSame(self::DESCRIPTION, $this->description([], '', self::DESCRIPTION));
    }

    public function testALongDescriptionIsShortened(): void
    {
        $result = $this->description(self::FORUM, '', str_repeat('a', 200));

        $this->assertSame(str_repeat('a', 140) . '...', $result);
        $this->assertSame(143, \strlen($result));
    }

    public function testADescriptionOf140CharactersIsKept(): void
    {
        $this->assertSame(str_repeat('a', 140), $this->description(self::FORUM, '', str_repeat('a', 140)));
    }

    public function testAMultiByteDescriptionIsShortenedByCharacters(): void
    {
        $result = $this->description(self::FORUM, '', str_repeat('é', 200));

        $this->assertTrue(mb_check_encoding($result, 'UTF-8'));
        $this->assertSame(143, mb_strlen($result));
        $this->assertStringEndsWith('...', $result);
    }

    public function testThePageNumberFollowsTheDescription(): void
    {
        $this->assertSame(self::DESCRIPTION . ' - Pages 2', $this->description(self::FORUM, 'Test Description', self::DESCRIPTION, self::GENERATED, ' - Pages 2'));
    }

    public function testThePageNumberIsNotAddedToTheGeneratedText(): void
    {
        $generated = 'Parent - Category - Pages 2 - Board';

        $this->assertSame($generated, $this->description([], '', '', $generated, ' - Pages 2'));
    }
}
