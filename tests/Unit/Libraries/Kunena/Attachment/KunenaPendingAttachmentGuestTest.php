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

namespace Kunena\Tests\Unit\Libraries\Kunena\Attachment;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Kunena\Forum\Libraries\Attachment\KunenaPendingAttachment;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * A pending upload (a file which waits for its message) goes into the message of the one who uploaded it. For a member that is
 * his user id. Every guest has the user id 0: a guest put the pending upload of another guest into his own message by sending its
 * number (the numbers follow each other). The upload of a guest belongs to his session now.
 */
class KunenaPendingAttachmentGuestTest extends UnitTestCase
{
    private mixed $savedApplication = null;

    protected function setUp(): void
    {
        $this->requireJoomla();

        $this->savedApplication = Factory::$application;
    }

    protected function tearDown(): void
    {
        Factory::$application = $this->savedApplication;
    }

    public static function uploads(): array
    {
        // The attachment, its uploader, the current user, the uploads of the session of the guest, may attach.
        return [
            'a member, his own upload'           => [10, 42, 42, [], true],
            'a member, the upload of another'    => [10, 43, 42, [], false],
            'a member, the upload of a guest'    => [10, 0, 42, [10], false],
            'a guest, an upload of his session'  => [10, 0, 0, [9, 10], true],
            'a guest, the upload of another one' => [11, 0, 0, [9, 10], false],
            'a guest without uploads'            => [10, 0, 0, [], false],
            'a guest, the upload of a member'    => [10, 42, 0, [10], false],
        ];
    }

    #[DataProvider('uploads')]
    public function testAPendingUploadGoesToTheOneWhoUploadedIt(int $id, int $uploader, int $user, array $session, bool $expected): void
    {
        $this->assertSame($expected, KunenaPendingAttachment::mayAttach($id, $uploader, $user, $session));
    }

    public function testTheUploadsOfAGuestAreKeptInHisSession(): void
    {
        $data    = [];
        $session = $this->createStub(Session::class);
        $session->method('get')->willReturnCallback(static function ($name, $default = null) use (&$data) {
            return $data[$name] ?? $default;
        });
        $session->method('set')->willReturnCallback(static function ($name, $value = null) use (&$data) {
            $data[$name] = $value;

            return null;
        });

        $app = $this->createStub(CMSApplication::class);
        $app->method('getSession')->willReturn($session);
        Factory::$application = $app;

        $this->assertSame([], KunenaPendingAttachment::guestUploads());

        KunenaPendingAttachment::rememberGuestUpload(10);
        KunenaPendingAttachment::rememberGuestUpload(12);
        KunenaPendingAttachment::rememberGuestUpload(10);
        KunenaPendingAttachment::rememberGuestUpload(0);

        $this->assertSame([10, 12], KunenaPendingAttachment::guestUploads());
    }

    public function testTheUploadAndTheMessageUseTheSession(): void
    {
        $controller = Files::read('src/site/src/Controllers/TopicController.php');
        $message    = Files::read('src/libraries/kunena/src/Forum/Message/KunenaMessage.php');

        $this->assertStringContainsString('KunenaPendingAttachment::rememberGuestUpload((int) $attachment->id);', $controller);
        $this->assertStringContainsString(
            'KunenaPendingAttachment::mayAttach((int) $attachment->id, (int) $attachment->userid, (int) $me->userid, $guestUploads)',
            $message
        );
    }

    public static function limits(): array
    {
        // The uploads of the user (or of the session of the guest), all the uploads of the user id, a guest, over the limit of 10.
        return [
            'a member below his limit'               => [9, 9, false, false],
            'a member at his limit'                  => [10, 10, false, true],
            'a guest without uploads, others have'   => [0, 60, true, false],
            'a guest at the limit of his session'    => [10, 10, true, true],
            'all the guests together at their limit' => [0, 100, true, true],
        ];
    }

    /**
     * The uploads which wait for a message have a limit. All the guests have the user id 0, so they shared one limit: the pending
     * uploads of a few guests stopped the uploads of every other guest. A guest has the limit of his session now (and all the
     * guests together a larger one).
     */
    #[DataProvider('limits')]
    public function testAGuestHasTheLimitOfHisOwnUploads(int $own, int $all, bool $guest, bool $expected): void
    {
        $this->assertSame($expected, KunenaPendingAttachment::overLimit($own, $all, 10, $guest));
    }

    public function testTheUploadCountsTheUploadsOfTheSessionOfAGuest(): void
    {
        $controller = Files::read('src/site/src/Controllers/TopicController.php');

        $this->assertStringContainsString('$mine = KunenaPendingAttachment::guestUploads();', $controller);
        $this->assertStringContainsString('KunenaPendingAttachment::overLimit($own, $pending, $maxPending, !$this->me->exists())', $controller);
    }
}
