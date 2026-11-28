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
 * The quick replies have no box "Notify me" for a member who follows the topic already, as the form of a reply: the box was shown
 * empty (from the defaults), and leaving it empty did not end the subscription. (The layouts need a whole site: their code is read.)
 */
class QuickReplySubscribedTest extends UnitTestCase
{
    public static function layouts(): array
    {
        return [
            'the quick reply'         => ['src/site/template/aurelia/layouts/message/edit/quickreply.php', 2],
            'the reply at the bottom' => ['src/site/template/aurelia/layouts/message/edit/full.php', 1],
        ];
    }

    #[DataProvider('layouts')]
    public function testNoBoxForAFollower(string $file, int $boxes): void
    {
        $source = Files::read($file);

        $this->assertSame($boxes, substr_count($source, "<?php if (\$topic->isAuthorised('subscribe') && !\$topic->getUserTopic()->subscribed) :"));
        $this->assertSame(0, substr_count($source, "<?php if (\$topic->isAuthorised('subscribe')) :"));
    }
}
