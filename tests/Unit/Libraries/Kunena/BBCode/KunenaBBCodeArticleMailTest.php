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

namespace Kunena\Tests\Unit\Libraries\Kunena\BBCode;

use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * An [article] gives nothing in the e-mails and in the activity streams, as the other tags of restricted content: the access to the
 * article was checked for who renders the post (the moderator who approves it, the queue of the mails), so an article of a restricted
 * access level went into the mail of every subscriber. (Without a site the old code fails on the application: it read the article.)
 */
class KunenaBBCodeArticleMailTest extends UnitTestCase
{
    protected function setUp(): void
    {
        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        if (!class_exists(KunenaBBCodeLibrary::class, false)) {
            require_once Files::path('src/libraries/kunena/src/BBCode/KunenaBBCode.php');
        }
    }

    public static function renderings(): array
    {
        return [
            'a mail of a subscription' => [(object) ['context' => 'subscription', 'parent' => (object) []]],
            'an activity stream'       => [(object) ['context' => '', 'parent' => (object) ['forceSecure' => true]]],
        ];
    }

    #[DataProvider('renderings')]
    public function testAnArticleGivesNothing(object $bbcode): void
    {
        $library = self::bare(KunenaBBCodeLibrary::class);

        $this->assertSame('', $library->DoArticle($bbcode, BBCode::BBCODE_OUTPUT, 'article', 'full', [], '42'));
    }
}
