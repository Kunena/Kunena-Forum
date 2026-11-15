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

namespace Kunena\Tests\Unit\Libraries\Kunena\Api;

use Kunena\Forum\Libraries\Api\KunenaApi;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The REST API of Kunena: what of the text of a post a user gets, the dates, the routes of the plugin (GET only), the models which
 * check the permissions, and the API part in the manifest, the package and the build.
 */
class KunenaApiTest extends UnitTestCase
{
    public function testTheConfidentialPrivateAndHiddenPartsAreNeverGiven(): void
    {
        $text = KunenaApi::safeMessage(
            'Visible [confidential]secret[/confidential] and [private]for a user[/private] and [hide]for members[/hide] end',
            false
        );

        $this->assertStringNotContainsString('secret', $text);
        $this->assertStringNotContainsString('for a user', $text);
        $this->assertStringNotContainsString('for members', $text);
        $this->assertStringContainsString('Visible', $text);
        $this->assertStringContainsString(' end', $text);
    }

    public function testAHiddenTextIsEmpty(): void
    {
        $this->assertSame('', KunenaApi::safeMessage('Anything', true));
    }

    public static function hidden(): array
    {
        // guest, first message, the category hides the replies from the guests, hidden
        return [
            'a guest, a reply, hidden replies'      => [true, false, true, true],
            'a guest, the first message'            => [true, true, true, false],
            'a guest, the replies are not hidden'   => [true, false, false, false],
            'a user, a reply, hidden replies'       => [false, false, true, false],
        ];
    }

    #[DataProvider('hidden')]
    public function testWhenTheTextIsHidden(bool $guest, bool $first, bool $hides, bool $hidden): void
    {
        $this->assertSame($hidden, KunenaApi::hidesText($guest, $first, $hides));
    }

    public function testTheDatesAreInUtc(): void
    {
        $this->assertSame('2023-11-14T22:13:20Z', KunenaApi::date(1700000000));
        $this->assertNull(KunenaApi::date(0));
        $this->assertNull(KunenaApi::date(null));
    }

    public function testThePluginHasOnlyRoutesWhichRead(): void
    {
        $source = Files::read('src/plugins/plg_webservices_kunena/src/Extension/Kunena.php');

        $this->assertSame(2, substr_count($source, 'new Route(['));
        $this->assertSame(2, substr_count($source, "new Route(['GET']"));
        $this->assertStringNotContainsString('createCRUDRoutes', $source);
        $this->assertStringContainsString("'v1/kunena/categories' => 'categories'", $source);
        $this->assertStringContainsString("'v1/kunena/topics'     => 'topics'", $source);
        $this->assertStringContainsString("'v1/kunena/posts'      => 'posts'", $source);
    }

    public static function controllers(): array
    {
        return [['Categories'], ['Topics'], ['Posts']];
    }

    #[DataProvider('controllers')]
    public function testTheControllersChangeNothing(string $name): void
    {
        $source = Files::read('src/api/src/Controller/' . $name . 'Controller.php');

        foreach (['add', 'edit', 'delete'] as $task) {
            $this->assertSame(1, preg_match('/public function ' . $task . '\(.*?\)\s*\{(.*?)\n    \}/s', $source, $m), $task);
            $this->assertStringContainsString("throw new RuntimeException('Method Not Allowed', 405);", $m[1]);
        }
    }

    public static function items(): array
    {
        return [
            'a category' => ['CategoryModel', "isAuthorised('read')"],
            'a topic'    => ['TopicModel', "isAuthorised('read')"],
            'a post'     => ['PostModel', "isAuthorised('read')"],
        ];
    }

    #[DataProvider('items')]
    public function testAnItemIsGivenOnlyWhenTheUserMayReadIt(string $model, string $check): void
    {
        $source = Files::read('src/api/src/Model/' . $model . '.php');

        $this->assertStringContainsString($check, $source);
        $this->assertStringContainsString('return null;', $source);
    }

    public function testTheListsUseTheAccessOfTheUser(): void
    {
        $this->assertStringContainsString("KunenaCategoryHelper::getCategories(false, false, 'read')", Files::read('src/api/src/Model/CategoriesModel.php'));
        $this->assertStringContainsString('->filterByUserAccess(KunenaUserHelper::getMyself())', Files::read('src/api/src/Model/TopicsModel.php'));
        $this->assertStringContainsString('->filterByUserAccess($me)', Files::read('src/api/src/Model/PostsModel.php'));
    }

    public function testThePermissionsOfTheForumAreGivenInTheApi(): void
    {
        // Without the access control of the plugin Kunena - Joomla, nobody may read anything (the lists of the API are empty).
        $this->assertStringContainsString(
            "if (\$app->isClient('cli') || \$app->isClient('api')) {",
            Files::read('src/plugins/plg_kunena_joomla/src/Extension/Joomla.php')
        );

        // The application of the API is an application of Kunena too.
        $this->assertStringContainsString(
            'public static function getApplication(string $client = \'\'): SiteApplication|AdministratorApplication|ApiApplication',
            Files::read('src/libraries/kunena/src/Factory/KunenaFactory.php')
        );
    }

    public function testTheComponentInstallsTheApiPart(): void
    {
        $xml = simplexml_load_file(Files::path('src/admin/kunena.xml'));

        $this->assertCount(1, $xml->xpath('/extension/api/files[@folder="webapi"]/folder[.="src"]'));
        $this->assertStringContainsString('todir="${tmp_dir}/packages/com_kunena/webapi"', Files::read('build.xml'));
    }

    public function testThePackageHasThePlugin(): void
    {
        $xml = simplexml_load_file(Files::path('src/pkg_kunena.xml'));

        $this->assertCount(1, $xml->xpath('//file[@type="plugin"][@group="webservices"][@id="kunena"]'));
        // The package of the plugin is made three times: its files, its xml with the version, its zip.
        $this->assertSame(3, substr_count(Files::read('build.xml'), '${tmp_dir}/packages/plg_webservices_kunena"'));
        $this->assertStringContainsString('dist/package/distribution/packages/plg_webservices_kunena_v${xml.version}', Files::read('build.xml'));
    }
}
