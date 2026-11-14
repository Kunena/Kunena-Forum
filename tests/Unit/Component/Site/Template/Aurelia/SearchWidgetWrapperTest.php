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

namespace Kunena\Tests\Unit\Component\Site\Template\Aurelia;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The search widget over the topic, the topics of a category and the user list is wrapped in a div with its own class, not in a
 * heading: a heading without heading text confuses screen readers. The styles of Aurelia and Agora select the wrapper by the class.
 */
class SearchWidgetWrapperTest extends UnitTestCase
{
    private const SEARCH = '\s*<\?php echo \$this->subLayout\(\'Widget\/Search\'\)';

    public static function layouts(): array
    {
        return [
            'topic'    => ['src/site/template/aurelia/layouts/topic/item/default.php'],
            'category' => ['src/site/template/aurelia/layouts/category/item/default.php'],
            'users'    => ['src/site/template/aurelia/layouts/user/list/default.php'],
        ];
    }

    #[DataProvider('layouts')]
    public function testTheSearchIsWrappedInADivWithTheClass(string $layout): void
    {
        $content = Files::read($layout);

        $this->assertMatchesRegularExpression('/<div class="float-end kunena-search-widget">' . self::SEARCH . '/', $content);
    }

    #[DataProvider('layouts')]
    public function testTheSearchIsNotInAHeading(string $layout): void
    {
        $content = Files::read($layout);

        $this->assertDoesNotMatchRegularExpression('/<h[1-6][^>]*>' . self::SEARCH . '/', $content);
    }

    public function testAureliaGivesTheWrapperItsBottomMargin(): void
    {
        $this->assertMatchesRegularExpression(
            '/\.kunena-search-widget\s*\{[^}]*margin-bottom:\s*0\.5rem;/',
            Files::read('src/site/template/aurelia/assets/scss/general.scss')
        );
    }

    public function testAgoraSelectsTheWrapperByTheClass(): void
    {
        foreach (['topic', 'mobile', 'forum'] as $name) {
            $content = Files::read('src/site/template/agora/assets/scss/' . $name . '.scss');

            $this->assertStringContainsString('.kunena-search-widget', $content, $name . '.scss does not select .kunena-search-widget.');

            if ($name !== 'forum') {
                $this->assertStringNotContainsString('> h2.float-end', $content, $name . '.scss still selects the search by > h2.float-end.');
            }
        }
    }
}
