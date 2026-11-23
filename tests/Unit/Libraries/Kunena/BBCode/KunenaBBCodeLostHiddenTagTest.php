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
 * The text of [hide], [confidential] and [private] after an earlier block of the same tag which was left open (a message which quotes
 * a message with an open [hide]): the parser counts the tag as lost, and the handlers then gave the block as text, "[hide]" and its
 * content, before any check of who reads, to a guest, in the e-mails and in the activity stream.
 */
class KunenaBBCodeLostHiddenTagTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';


    protected function setUp(): void
    {
        // The parser is in the folder External of the library, and the library of the tags is in the file of KunenaBBCode.
        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        // The file of KunenaBBCode loads the parser from the folder of the library.
        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        if (!class_exists(KunenaBBCodeLibrary::class, false)) {
            require_once Files::path(self::BBCODE);
        }
    }

    /**
     * The parser of a subscription mail after an earlier [hide], [confidential] or [private] of the message was left open: nbbc
     * closed it and counts the tag as lost for the rest of the message.
     */
    private function mailAfterALostTag(string $name): object
    {
        return (object) [
            'parent'           => (object) ['forceMinimal' => false, 'forceSecure' => false, 'inline_attachments' => []],
            'lost_start_tags'  => [$name => 1],
            'was_limited'      => false,
            'autoLink_disable' => 0,
            'context'          => 'subscription',
        ];
    }

    public static function hiddenTags(): array
    {
        return [
            'hide'         => ['DoHide', 'hide'],
            'confidential' => ['DoConfidential', 'confidential'],
            'private'      => ['DoPrivate', 'private'],
        ];
    }

    #[DataProvider('hiddenTags')]
    public function testAHiddenBlockAfterALostOneStaysHidden(string $handler, string $name): void
    {
        $html = self::bare(KunenaBBCodeLibrary::class)
            ->$handler($this->mailAfterALostTag($name), BBCode::BBCODE_OUTPUT, $name, '', [], 'SECRETTEXT');

        $this->assertStringNotContainsString('SECRETTEXT', (string) $html);
    }
}
