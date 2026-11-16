<?php

/**
 * Kunena Component
 *
 * @package  Kunena.Tests
 * @license  https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Tests\Unit\User;

use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\Error\KunenaError;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Forum\Libraries\User\KunenaUserSocials;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class SocialsSchemaTest extends TestCase
{
    protected function setUp(): void
    {
        if (!\defined('_JEXEC')) {
            \define('_JEXEC', 1);
        }

        require_once __DIR__ . '/fixtures/socials_schema_stubs.php';
        require_once __DIR__ . '/../../../src/libraries/kunena/src/User/KunenaUserSocials.php';

        KunenaUserHelper::$user = (object) ['userid' => 42, 'socials' => ''];
        Factory::$application = new class () {
            public array $messages = [];

            public function enqueueMessage($message, $type): void
            {
                $this->messages[] = [$message, $type];
            }
        };
        Factory::$database = new SocialsDatabaseStub();
    }

    public function testMissingColumnWarnsAndDoesNotUpdate(): void
    {
        KunenaUserSocials::addSocialsParams();

        self::assertSame('prefix_kunena_users', Factory::$database->inspectedTable);
        self::assertFalse(Factory::$database->executed);
        self::assertSame([['COM_KUNENA_SOCIALS_DATABASE_UPDATE_REQUIRED', 'error']], Factory::$application->messages);
    }

    public function testExistingColumnInitializesTheCurrentUser(): void
    {
        Factory::$database->columns = ['userid' => 'int', 'socials' => 'text'];

        KunenaUserSocials::addSocialsParams();

        self::assertTrue(Factory::$database->executed);
        self::assertSame('`#__kunena_users`', Factory::$database->query->table);
        self::assertSame(['`socials` = ' . Factory::$database->quote(KunenaUserSocials::getDefaultSocialsJson())], Factory::$database->query->fields);
        self::assertSame(['`userid` = 42'], Factory::$database->query->conditions);
        self::assertSame([], Factory::$application->messages);
    }

    public function testGuestsAndInitializedUsersDoNotInspectTheSchema(): void
    {
        foreach ([(object) ['userid' => 0, 'socials' => ''], (object) ['userid' => 42, 'socials' => '{}']] as $user) {
            KunenaUserHelper::$user = $user;
            KunenaUserSocials::addSocialsParams();
        }

        self::assertNull(Factory::$database->inspectedTable);
        self::assertFalse(Factory::$database->executed);
        self::assertSame([], Factory::$application->messages);
    }

    public function testDatabaseFailuresAreReported(): void
    {
        $failure = new \RuntimeException('Database unavailable');
        Factory::$database->failure = $failure;

        KunenaUserSocials::addSocialsParams();

        self::assertSame($failure, KunenaError::$error);
        self::assertFalse(Factory::$database->executed);
        self::assertSame([], Factory::$application->messages);
    }

    public function testUpdateFailuresAreReported(): void
    {
        $failure = new \RuntimeException('Update failed');
        Factory::$database->columns = ['userid' => 'int', 'socials' => 'text'];
        Factory::$database->executionFailure = $failure;

        KunenaUserSocials::addSocialsParams();

        self::assertSame($failure, KunenaError::$error);
        self::assertSame([], Factory::$application->messages);
    }
}

class SocialsDatabaseStub
{
    public array $columns = ['userid' => 'int'];
    public ?string $inspectedTable = null;
    public ?\RuntimeException $failure = null;
    public ?\RuntimeException $executionFailure = null;
    public bool $executed = false;
    public $query;

    public function replacePrefix($table)
    {
        return str_replace('#__', 'prefix_', $table);
    }

    public function getTableColumns($table)
    {
        $this->inspectedTable = $table;

        if ($this->failure) {
            throw $this->failure;
        }

        return $this->columns;
    }

    public function createQuery()
    {
        $this->query = new class () {
            public $table;
            public $fields;
            public $conditions;

            public function update($table)
            {
                $this->table = $table;

                return $this;
            }

            public function set($fields)
            {
                $this->fields = $fields;

                return $this;
            }

            public function where($conditions)
            {
                $this->conditions = $conditions;

                return $this;
            }
        };

        return $this->query;
    }

    public function quoteName($name)
    {
        return '`' . $name . '`';
    }

    public function quote($value)
    {
        return "'" . $value . "'";
    }

    public function setQuery($query): void
    {
    }

    public function execute(): void
    {
        if ($this->executionFailure) {
            throw $this->executionFailure;
        }

        $this->executed = true;
    }
}
