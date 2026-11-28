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

namespace Kunena\Tests\Unit\Plugin\Kunena\Easysocial;

use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Plugin\Kunena\Easysocial\Helper\KunenaActivityEasySocial;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The points and the badges of EasySocial for a post go to its author: they went to the user of the request, so the moderator who
 * approved or restored a post got them, and the author got nothing. The mail of a reply leaves out its author, not the user of the
 * request (the author was told about his own reply, the moderator was left out).
 */
class KunenaActivityEasySocialAuthorTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();

        // EasySocial, as far as the activity uses it: the users who get points and badges are written down (null: the user of the
        // request, which FD::user() takes then).
        if (!class_exists('FD', false)) {
            eval(<<<'PHP'
                class FD
                {
                    public static array $given = [];

                    public static function user($id = null)
                    {
                        return (object) ['id' => $id ?? 'the user of the request'];
                    }

                    public static function points()
                    {
                        return new class () {
                            public function assign($command, $extension, $userid)
                            {
                                \FD::$given[] = ['points', $userid];
                            }
                        };
                    }

                    public static function badges()
                    {
                        return new class () {
                            public function log($extension, $command, $actor, $target)
                            {
                                \FD::$given[] = ['badge', $target];
                            }
                        };
                    }

                    public static function stream()
                    {
                        return new class () {
                            public function getTemplate()
                            {
                                return new class () {
                                    public function __call($name, $arguments)
                                    {
                                        return $this;
                                    }
                                };
                            }

                            public function add($template)
                            {
                            }
                        };
                    }
                }
                PHP);
        }

        if (!\defined('SOCIAL_TYPE_USER')) {
            \define('SOCIAL_TYPE_USER', 'user');
        }

        require_once Files::path('src/plugins/plg_kunena_easysocial/src/Helper/KunenaActivityEasySocial.php');

        \FD::$given = [];
    }

    public function testTheAuthorOfANewTopicGetsThePointsAndTheBadge(): void
    {
        $message = self::bare(KunenaMessage::class, ['id' => 5, 'thread' => 5, 'userid' => 42, 'message' => 'A new topic with some words.']);

        (new KunenaActivityEasySocial(new Registry()))->onAfterPost($message);

        $this->assertSame([['points', 42], ['badge', 42]], \FD::$given);
    }

    public function testTheReplyAndItsMailsHaveTheAuthorToo(): void
    {
        $source = Files::read('src/plugins/plg_kunena_easysocial/src/Helper/KunenaActivityEasySocial.php');

        $this->assertStringContainsString("\$this->assignPoints('thread.reply', (int) \$message->userid);", $source);
        $this->assertStringContainsString("Text::_('PLG_KUNENA_EASYSOCIAL_BADGE_REPLY_TITLE'), (int) \$message->userid);", $source);
        $this->assertStringContainsString('$mailadmins, (int) $message->userid);', $source);
    }
}
