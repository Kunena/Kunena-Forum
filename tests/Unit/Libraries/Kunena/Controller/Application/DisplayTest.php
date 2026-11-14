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

namespace Kunena\Tests\Unit\Libraries\Kunena\Controller\Application;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * A visitor of a forum for registered users only gets the 403 page with the login form of the forum below the message
 * (issue #9675), and the offline page stays as it was.
 */
class DisplayTest extends UnitTestCase
{
    private const FILE = 'src/libraries/kunena/src/Controller/Application/Display.php';

    /**
     * The code of one branch of execute(): from its condition to the next branch.
     */
    private function branch(string $condition, string $next): string
    {
        $source = Files::read(self::FILE);
        $start  = strpos($source, $condition);

        $this->assertNotFalse($start, 'The branch ' . $condition . ' is in execute().');

        $end = strpos($source, $next, $start + \strlen($condition));

        $this->assertNotFalse($end, 'The branch ' . $condition . ' ends with ' . $next . '.');

        return substr($source, $start, $end - $start);
    }

    private function regOnlyBranch(): string
    {
        return $this->branch('} elseif ($this->config->regOnly && !$this->me->exists()) {', '} else {');
    }

    public function testTheRegisteredOnlyPageKeepsStatus403AndThePageLayoutOffline(): void
    {
        $branch = $this->regOnlyBranch();

        $this->assertStringContainsString('$this->setResponseStatus(403);', $branch);
        $this->assertStringContainsString("\$this->output->setLayout('offline');", $branch);
    }

    public function testTheRegisteredOnlyPageShowsTheMessageAndThenTheLoginForm(): void
    {
        $branch = $this->regOnlyBranch();

        $custom = strpos($branch, "KunenaLayout::factory('Widget/Custom')");
        $login  = strpos($branch, "KunenaLayout::factory('Widget/Login/Login')->setLayout('login')");

        $this->assertNotFalse($custom);
        $this->assertNotFalse($login);
        $this->assertLessThan($login, $custom);
        $this->assertStringContainsString("->set('header', Text::_('COM_KUNENA_LOGIN_NOTIFICATION'))", $branch);
        $this->assertStringContainsString("->set('body', Text::_('COM_KUNENA_LOGIN_FORUM'))", $branch);
        $this->assertStringContainsString('$this->content->appendAfter(', $branch);
    }

    public function testTheRegisteredOnlyPageRendersNothingElseOfTheForum(): void
    {
        $branch = $this->regOnlyBranch();

        $this->assertStringNotContainsString('$this->display()', $branch);
        $this->assertStringNotContainsString('breadcrumb', $branch);
    }

    public function testTheOfflinePageHasNoLoginForm(): void
    {
        $branch = $this->branch('if ($this->config->boardOffline && !$this->me->isAdmin()) {', '} elseif (');

        $this->assertStringContainsString('$this->setResponseStatus(503);', $branch);
        $this->assertStringContainsString("->set('header', Text::_('COM_KUNENA_FORUM_IS_OFFLINE'))", $branch);
        $this->assertStringNotContainsString('Widget/Login/Login', $branch);
    }
}
