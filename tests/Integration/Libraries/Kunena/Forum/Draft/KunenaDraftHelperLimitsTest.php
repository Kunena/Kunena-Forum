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

namespace Kunena\Tests\Integration\Libraries\Kunena\Forum\Draft;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Kunena\Forum\Libraries\Forum\Draft\KunenaDraftHelper;
use Kunena\Tests\Integration\DBTestInterface;
use Kunena\Tests\Integration\DBTestTrait;
use Kunena\Tests\Integration\IntegrationTestCase;

/**
 * A draft keeps every field within a limit, not only its text: the private part as the text, the short fields as a subject, the poll
 * with at most 100 options. A private part of megabytes was kept in each of the 50 drafts of a user, and one over the size of the
 * column ended in an error of the database shown to the user.
 */
class KunenaDraftHelperLimitsTest extends IntegrationTestCase implements DBTestInterface
{
    use DBTestTrait;

    private mixed $container;

    public function getSchemasToLoad(): array
    {
        return ['kunena'];
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!\defined('JVERSION')) {
            $this->markTestSkipped('The framework of Joomla is not loaded: set KUNENA_TEST_JOOMLA to a Joomla installation.');
        }

        $driver    = $this->getDBDriver();
        $container = new Container();
        $container->share('DatabaseDriver', static fn () => $driver);
        $container->share(DatabaseInterface::class, static fn () => $driver);

        $this->container    = Factory::$container;
        Factory::$container = $container;
    }

    protected function tearDown(): void
    {
        if (\defined('JVERSION')) {
            Factory::$container = $this->container;
            $this->getDBDriver()->setQuery('DELETE FROM #__kunena_drafts WHERE user_id = 42')->execute();
        }

        parent::tearDown();
    }

    public function testEveryFieldOfADraftHasALimit(): void
    {
        $id = KunenaDraftHelper::save(42, [
            'subject'         => 'Draft',
            'message'         => 'Text',
            'private'         => str_repeat('p', KunenaDraftHelper::MAX_LENGTH + 10),
            'modified_reason' => str_repeat('r', 1000),
            'poll_options'    => array_fill(0, 150, str_repeat('o', 300)),
        ]);

        $this->assertGreaterThan(0, $id);

        $fields = json_decode((string) $this->fetchAll('SELECT fields FROM #__kunena_drafts WHERE id = ' . (int) $id)[0]['fields'], true);

        $this->assertSame(KunenaDraftHelper::MAX_LENGTH, mb_strlen($fields['private']));
        $this->assertSame(255, mb_strlen($fields['modified_reason']));
        $this->assertCount(100, $fields['poll_options']);
        $this->assertSame(255, mb_strlen(reset($fields['poll_options'])));
    }
}
