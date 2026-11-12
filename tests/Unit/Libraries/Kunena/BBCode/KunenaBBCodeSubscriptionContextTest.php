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

use Kunena\Forum\Libraries\Attachment\KunenaAttachment;
use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The e-mails of the subscriptions render the message in the context "subscription", in which an attachment gives nothing. Without
 * it, an attachment which the receiver (or the queue which sends the mail) may not read printed the notice of missing permissions.
 */
class KunenaBBCodeSubscriptionContextTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    private const LAYOUTS = [
        'src/site/template/aurelia/layouts/email/subscription/default.php',
        'src/site/template/aurelia/layouts/email/subscription/moderator.php',
        'src/site/template/system/layouts/email/subscription/default.php',
        'src/site/template/system/layouts/email/subscription/moderator.php',
    ];

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

    private function subscription(): object
    {
        return (object) [
            'parent'           => (object) ['forceMinimal' => false, 'forceSecure' => true, 'inline_attachments' => []],
            'lost_start_tags'  => [],
            'was_limited'      => false,
            'autoLink_disable' => 0,
            'context'          => 'subscription',
        ];
    }

    public static function attachmentTags(): array
    {
        return [
            'attachment id'          => ['12', ''],
            'attachment id and name' => ['12', 'report.pdf'],
            'no id, a name'          => [false, 'report.pdf'],
            'no id, no content'      => [false, ''],
        ];
    }

    #[DataProvider('attachmentTags')]
    public function testAttachmentTagGivesNothingInASubscriptionMail(string|false $default, string $content): void
    {
        $html = self::bare(KunenaBBCodeLibrary::class)
            ->DoAttachment($this->subscription(), BBCode::BBCODE_OUTPUT, 'attachment', $default, [], $content);

        $this->assertSame('', $html);
    }

    public function testRenderedAttachmentGivesNothingInASubscriptionMail(): void
    {
        $html = self::invoke(
            self::bare(KunenaBBCodeLibrary::class),
            'renderAttachment',
            self::bare(KunenaAttachment::class),
            $this->subscription()
        );

        $this->assertSame('', $html);
    }

    public function testEveryHtmlMessageOfTheSubscriptionMailsHasTheContext(): void
    {
        foreach (self::LAYOUTS as $layout) {
            $source = (string) file_get_contents(Files::path($layout));

            preg_match_all("/displayField\\('message'[^)]*\\)/", $source, $calls);

            $html = array_values(array_filter($calls[0], static fn ($call) => !str_contains($call, 'false')));

            $this->assertNotEmpty($html, $layout . ' renders the message in HTML.');

            foreach ($html as $call) {
                $this->assertSame("displayField('message', true, 'subscription')", $call, $layout);
            }
        }
    }
}
