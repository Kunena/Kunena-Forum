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

namespace Kunena\Tests\Unit\Libraries\Kunena\User;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Language;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\Event\Dispatcher;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use Kunena\Forum\Libraries\User\KunenaUser;
use Kunena\Forum\Libraries\User\KunenaUserHelper;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use ReflectionProperty;

/**
 * The setting of a member "Display Age Or Birthdate" was read nowhere: a member who chose his age saw his whole birthdate on his
 * profile and next to each of his posts, for every visitor. With the age chosen the others see the age; he sees his birthdate.
 */
class KunenaUserBirthdateTest extends UnitTestCase
{
    private array $saved = [];

    protected function setUp(): void
    {
        $this->requireJoomla();

        $site = $this->createStub(CMSApplicationInterface::class);
        $site->method('getDispatcher')->willReturn(new Dispatcher());

        $this->saved = [
            'config'      => (new ReflectionProperty(KunenaConfig::class, 'instance'))->getValue(),
            'application' => Factory::$application,
        ];

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, self::bare(KunenaConfig::class, ['config' => []]));
        Factory::$application = $site;

        // Loading the helper makes the current user: it needs a database and the avatars.
        if (!class_exists(KunenaUserHelper::class, false)) {
            $db = $this->createStub(DatabaseDriver::class);
            $db->method('getTableColumns')->willReturn(['userid' => 'int']);
            $avatars   = new ReflectionProperty(KunenaAvatar::class, 'instance');
            $container = Factory::$container;
            $oldAvatar = $avatars->getValue();

            Factory::$container = (new Container())->set(DatabaseInterface::class, $db)->set('DatabaseDriver', $db);
            $avatars->setValue(null, new KunenaAvatar());

            try {
                class_exists(KunenaUserHelper::class);
            } finally {
                Factory::$container = $container;
                $avatars->setValue(null, $oldAvatar);
            }
        }

        $this->saved['me'] = (new ReflectionProperty(KunenaUserHelper::class, '_me'))->getValue();
        (new ReflectionProperty(KunenaUserHelper::class, '_me'))->setValue(null, self::bare(KunenaUser::class, ['userid' => 9]));
    }

    protected function tearDown(): void
    {
        if (!$this->saved) {
            return;
        }

        (new ReflectionProperty(KunenaConfig::class, 'instance'))->setValue(null, $this->saved['config']);
        (new ReflectionProperty(KunenaUserHelper::class, '_me'))->setValue(null, $this->saved['me']);
        Factory::$application = $this->saved['application'];
    }

    private function member(int $display): KunenaUser
    {
        return self::bare(KunenaUser::class, ['userid' => 5, 'birthdate' => '2000-06-15', 'displayAgeBirthdate' => $display]);
    }

    public function testAMemberWhoChoseHisAgeShowsHisAgeToTheOthers(): void
    {
        $this->assertFalse($this->member(0)->showsBirthdate());
        $this->assertTrue($this->member(1)->showsBirthdate());

        // He sees his own birthdate.
        (new ReflectionProperty(KunenaUserHelper::class, '_me'))->setValue(null, self::bare(KunenaUser::class, ['userid' => 5]));
        $this->assertTrue($this->member(0)->showsBirthdate());
    }

    public function testTheAgeIsCountedFromTheBirthdate(): void
    {
        $this->assertSame(25, $this->member(0)->getAge(gmmktime(12, 0, 0, 6, 14, 2026)));
        $this->assertSame(26, $this->member(0)->getAge(gmmktime(12, 0, 0, 6, 15, 2026)));
        $this->assertNull(self::bare(KunenaUser::class, ['birthdate' => '1000-01-01'])->getAge());
    }

    /**
     * The icon next to the posts (its tooltip had the birthdate): the icon needs the template of a site, its code is read.
     */
    public function testTheIconNextToThePostsHasTheAgeOnly(): void
    {
        $source = Files::read('src/libraries/kunena/src/User/KunenaUser.php');

        $this->assertSame(1, preg_match("/case 'birthdate':\\s*if \\(!\\\$this->birthdate(.*?)break;/s", $source, $m));
        $this->assertStringContainsString('$this->showsBirthdate()', $m[1]);
        $this->assertStringContainsString("Text::sprintf('COM_KUNENA_MYPROFILE_AGE_AGE', (int) \$this->getAge())", $m[1]);
        $this->assertStringContainsString('COM_KUNENA_MYPROFILE_AGE_AGE = ', Files::read('src/site/language/en-GB/com_kunena.ini'));
    }

    public function testTheProfilesOfBothTemplatesShowTheAgeWhenItWasChosen(): void
    {
        foreach (
            [
                'src/site/template/agora/layouts/user/item/summary/default.php'   => '$this->profile',
                'src/site/template/aurelia/layouts/user/item/summary/default.php' => '$this->profile',
                'src/site/template/aurelia/layouts/user/profile/horizontal.php'   => '$user',
            ] as $file => $profile
        ) {
            $source = Files::read($file);

            $this->assertStringContainsString('<?php if (' . $profile . '->showsBirthdate()) : ?>', $source, $file);
            $this->assertStringContainsString($profile . '->getAge()', $source, $file);
        }
    }
}
