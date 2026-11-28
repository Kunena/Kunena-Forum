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
 * The mail of a subscription of the fallback layout (the template system, for a template without its own layout) has the link to
 * unsubscribe in its HTML and its plain text, as the layout of Aurelia: it had only the header List-Unsubscribe.
 */
class SystemMailUnsubscribeTest extends UnitTestCase
{
    public function testBothPartsHaveTheLinkToUnsubscribe(): void
    {
        foreach (['aurelia', 'system'] as $template) {
            $layout = Files::read("src/site/template/{$template}/layouts/email/subscription/default.php");

            $this->assertStringContainsString("<a href=\"<?php echo htmlspecialchars(\$this->unsubscribeUrl, ENT_QUOTES, 'UTF-8'); ?>\"", $layout, $template);
            $this->assertStringContainsString("{\$this->text('COM_KUNENA_POST_EMAIL_NOTIFICATION3')}{\$unsubscribe}", $layout, $template);
        }
    }
}
