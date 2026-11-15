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
 * The link to the message in the e-mails (Kunena 7.1, #10320): a notification sent from the queue (by the scheduled task) has the
 * address which was stored with it, so every link of the e-mail, the button too, is $this->messageLink and not the address of the
 * request. The address has & in it: in the html it is encoded.
 */
class EmailMessageLinkTest extends UnitTestCase
{
    public static function layouts(): array
    {
        $layouts = [];

        foreach (['aurelia', 'system'] as $template) {
            foreach (['subscription/default', 'subscription/moderator', 'report/default'] as $layout) {
                $layouts[$template . ' ' . $layout] = ['src/site/template/' . $template . '/layouts/email/' . $layout . '.php'];
            }
        }

        return $layouts;
    }

    #[DataProvider('layouts')]
    public function testEveryLinkToTheMessageIsTheEncodedMessageLink(string $layout): void
    {
        $source = Files::read($layout);

        $this->assertStringNotContainsString('echo $this->messageLink;', $source);
        $this->assertStringNotContainsString('href="<?php echo Uri::getInstance()', $source);
        $this->assertGreaterThanOrEqual(2, substr_count($source, "htmlspecialchars((string) \$this->messageLink, ENT_QUOTES, 'UTF-8')"));
    }

    public static function queued(): array
    {
        return [
            'aurelia subscription' => ['src/site/template/aurelia/layouts/email/subscription/default.php'],
            'aurelia moderator'    => ['src/site/template/aurelia/layouts/email/subscription/moderator.php'],
            'system subscription'  => ['src/site/template/system/layouts/email/subscription/default.php'],
            'system moderator'     => ['src/site/template/system/layouts/email/subscription/moderator.php'],
        ];
    }

    #[DataProvider('queued')]
    public function testAQueuedNotificationUsesItsStoredAddress(string $layout): void
    {
        $this->assertStringContainsString("\$this->messageLink = (\$this->messageUrl ?? '') ?:", Files::read($layout));
    }
}
