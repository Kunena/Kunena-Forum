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
 * The login form of the forum (on the page for registered users only and on a protected category) has the Bootstrap 5 markup
 * of Aurelia, which Agora inherits, and its heading can be left out where the page already shows the message (issue #9675).
 */
class WidgetLoginLoginTest extends UnitTestCase
{
    private const AURELIA = 'src/site/template/aurelia/layouts/widget/login/login/login.php';

    private const SYSTEM = 'src/site/template/system/layouts/widget/login/login/login.php';

    public static function layouts(): array
    {
        return [
            'aurelia' => [self::AURELIA],
            'system'  => [self::SYSTEM],
        ];
    }

    public function testTheAureliaFormUsesBootstrap5(): void
    {
        $source = Files::read(self::AURELIA);

        foreach (['form-control', 'input-group', 'input-group-text', 'visually-hidden', 'btn btn-outline-primary'] as $class) {
            $this->assertStringContainsString($class, $source);
        }

        foreach (['input-prepend', 'input-append', 'add-on', 'element-invisible', 'input-small', 'control-group', 'form-inline'] as $class) {
            $this->assertStringNotContainsString($class, $source);
        }
    }

    public function testTheAureliaFormKeepsTheFieldsOfTheLogin(): void
    {
        $source = Files::read(self::AURELIA);

        $this->assertStringContainsString('<div class="kloginform">', $source);
        $this->assertStringContainsString('id="klogin-username-full" type="text" name="username"', $source);
        $this->assertStringContainsString('id="klogin-passwd-full" type="password" name="password"', $source);
        $this->assertStringContainsString('<label for="klogin-username-full" class="visually-hidden">', $source);
        $this->assertStringContainsString('<label for="klogin-passwd-full" class="visually-hidden">', $source);
        $this->assertStringContainsString('<input type="hidden" name="view" value="user"/>', $source);
        $this->assertStringContainsString('<input type="hidden" name="task" value="login"/>', $source);
        $this->assertStringContainsString("HTMLHelper::_('form.token')", $source);
        $this->assertStringContainsString('<button type="submit"', $source);
    }

    #[DataProvider('layouts')]
    public function testTheHeadingIsShownUnlessTheLayoutIsToldNotTo(string $file): void
    {
        $source = Files::read($file);

        $this->assertStringContainsString('$showHeading = isset($this->showHeading) ? $this->showHeading : true;', $source);
        $this->assertMatchesRegularExpression(
            "/<\\?php if \\(\\\$showHeading\\) :\\s*\\?>\\s*<h1[^>]*><\\?php echo Text::_\\('COM_KUNENA_LOGIN_FORUM'\\); \\?><\\/h1>\\s*<\\?php endif; \\?>/",
            $source
        );
    }
}
