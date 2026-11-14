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

namespace Kunena\Tests\Unit\Component\Site\Template;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The follower count and the Follow / Ignore / Edit buttons of the profile are wrapped in a div with its own class, not in a
 * heading: they are no heading text. The styles of Aurelia and Agora give the div the text metrics the heading had. The search
 * input of the topic has its own id, not the id of the search module of Joomla.
 */
class ProfileActionsWrapperTest extends UnitTestCase
{
    // Split, so that a grep for the old id over the repository does not find this test.
    private const OLD_ID = 'mod-search-' . 'searchword';

    public function testAureliaWrapsTheProfileActionsInADiv(): void
    {
        $content = Files::read('src/site/template/aurelia/layouts/user/item/default.php');

        $this->assertStringContainsString('<div class="float-end kunena-profile-actions">', $content);
        $this->assertStringNotContainsString('<h2 class="float-end">', $content);
        $this->assertStringNotContainsString('<h2', $content);
    }

    public function testAgoraWrapsTheProfileActionsInADiv(): void
    {
        $content = Files::read('src/site/template/agora/layouts/user/item/default.php');

        $this->assertStringContainsString('<div class="agora-profile-actions">', $content);
        $this->assertStringNotContainsString('<h2 class="agora-profile-actions">', $content);
    }

    public function testAureliaGivesTheWrapperTheSizeOfTheHeading(): void
    {
        $this->assertMatchesRegularExpression(
            '/\.kunena-profile-actions\s*\{[^}]*font-size:\s*20px;/',
            Files::read('src/site/template/aurelia/assets/scss/general.scss')
        );
    }

    public function testAgoraGivesTheWrapperTheWeightOfTheHeading(): void
    {
        $this->assertMatchesRegularExpression(
            '/\.agora-profile-actions\s*\{[^}]*font-weight:\s*650;/',
            Files::read('src/site/template/agora/assets/scss/profile.scss')
        );
    }

    public function testTheTopicSearchInputHasItsOwnId(): void
    {
        $content = Files::read('src/site/template/aurelia/layouts/widget/search/topic.php');

        $this->assertMatchesRegularExpression('/<input name="query"[^>]*id="kunena-search-topic-query"/', $content);
        $this->assertStringNotContainsString(self::OLD_ID, $content);
    }
}
