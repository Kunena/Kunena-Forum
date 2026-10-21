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

namespace Kunena\Tests\Unit\Codebase;

use FilesystemIterator;
use Kunena\Tests\Unit\Support\Files;
use PHPUnit\Framework\Attributes\DataProvider;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Kunena\Tests\Unit\UnitTestCase;

/**
 * The pages and the layouts of the template ask for controllers ($this->execute('Topic/Item'), $this->subRequest('Widget/Login')):
 * every controller which is asked for exists, with the name (and the case of the letters) which the request gives it. The pages of
 * the forum are listed too.
 */
class PageControllersTest extends UnitTestCase
{
    private const TEMPLATE = 'src/site/template/aurelia';

    /**
     * Requests of a template for which there is no controller (found by the first run of this test): the page asks for
     * a controller which does not exist, so the page shows an error. A new one fails; one which gets a controller is taken out.
     */
    private const KNOWN_MISSING = ['Statistics/WhoIsOnline'];

    /**
     * Requests whose controller is in a folder and a file with another case than the class name which KunenaRequest makes (it
     * writes every word with a capital and the rest small: Topic/ModLog is Topic, Modlog, TopicModlogDisplay). That works on Windows
     * and macOS, but a server with a case sensitive file system does not find the controller.
     */
    private const KNOWN_CASE = ['Topic/ModLog'];

    /**
     * The pages of the forum: view/layout of pages/<view>/<layout>/default.php. A new page is added here (and gets its tests).
     */
    private const PAGES = [
        'announcement/create', 'announcement/default', 'announcement/edit', 'announcement/listing',
        'category/create', 'category/default', 'category/list', 'category/manage', 'category/user',
        'credits/default', 'inbox/default', 'misc/default', 'search/default',
        'statistics/default', 'statistics/whoisonline',
        'topic/create', 'topic/default', 'topic/drafts', 'topic/edit', 'topic/moderate', 'topic/modlog', 'topic/poll',
        'topic/reply', 'topic/report', 'topic/unsubscribe', 'topic/vote',
        'topics/default', 'topics/moderator', 'topics/posts', 'topics/unread', 'topics/user',
        'user/attachments', 'user/banmanager', 'user/default', 'user/edit', 'user/list', 'user/moderate',
    ];

    /** @var array<string,string[]>|null */
    private static ?array $requests = null;

    /**
     * The requests for controllers in the files of the template, with the files which ask for them.
     *
     * @return  array<string,string[]>
     */
    private static function requests(): array
    {
        if (self::$requests === null) {
            self::$requests = [];
            $root           = str_replace('\\', '/', Files::path(self::TEMPLATE));

            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)) as $file) {
                $path = str_replace('\\', '/', $file->getPathname());

                if (!str_ends_with($path, '.php')) {
                    continue;
                }

                if (preg_match_all('/->(?:execute|subRequest)\(\s*[\'"]([\w\/]+)[\'"]/', (string) file_get_contents($path), $matches)) {
                    foreach ($matches[1] as $name) {
                        self::$requests[$name][] = substr($path, \strlen($root) + 1);
                    }
                }
            }

            ksort(self::$requests);
        }

        return self::$requests;
    }

    /**
     * The file of the controller of a request, as KunenaRequest names the class: every word with a capital and the rest small.
     * Topic/Item is Controller/Topic/Item/TopicItemDisplay.php, and a request of one part (Credits) is
     * Controller/Credits/Display/CreditsDisplay.php.
     */
    private static function controllerFile(string $request): string
    {
        $parts = array_map(static fn ($part) => ucfirst(strtolower($part)), explode('/', $request));
        $class = implode('', $parts) . 'Display';

        if (\count($parts) === 1) {
            $parts[] = 'Display';
        }

        return 'src/site/src/Controller/' . implode('/', $parts) . '/' . $class . '.php';
    }

    /**
     * Does the file exist with exactly this case of every folder and of the file (like on a case sensitive file system)?
     */
    private static function existsWithCase(string $relative): bool
    {
        $directory = Files::root();

        foreach (explode('/', $relative) as $segment) {
            $entries = scandir($directory);

            if ($entries === false || !\in_array($segment, $entries, true)) {
                return false;
            }

            $directory .= '/' . $segment;
        }

        return true;
    }

    public static function requestNames(): array
    {
        $cases = [];

        foreach (array_keys(self::requests()) as $name) {
            $cases[$name] = [$name];
        }

        return $cases;
    }

    public static function pages(): array
    {
        $cases = [];

        foreach (self::PAGES as $page) {
            $cases[$page] = [$page];
        }

        return $cases;
    }

    public function testTheTemplateAsksForManyControllers(): void
    {
        $this->assertGreaterThan(30, \count(self::requests()));
    }

    #[DataProvider('requestNames')]
    public function testTheControllerOfTheRequestExists(string $request): void
    {
        $file = self::controllerFile($request);

        if (\in_array($request, self::KNOWN_MISSING, true)) {
            $this->assertFileDoesNotExist(Files::path($file), "The controller of $request exists now: take it out of KNOWN_MISSING.");

            return;
        }

        $this->assertFileExists(Files::path($file), "$request is asked for by " . implode(', ', self::requests()[$request]) . " but $file does not exist.");

        if (\in_array($request, self::KNOWN_CASE, true)) {
            $this->assertFalse(self::existsWithCase($file), "The controller of $request has the right case now: take it out of KNOWN_CASE.");

            return;
        }

        $this->assertTrue(self::existsWithCase($file), "$file exists only with another case of its letters: a server with a case sensitive file system does not find it.");
    }

    #[DataProvider('requestNames')]
    public function testTheClassOfTheControllerHasTheNameOfItsFile(string $request): void
    {
        $file = self::controllerFile($request);

        if (!is_file(Files::path($file))) {
            $this->assertContains($request, self::KNOWN_MISSING);

            return;
        }

        $code = Files::read($file);

        // The case of the letters can differ (the known case problems): the name is the same.
        $this->assertMatchesRegularExpression('/^class\s+' . preg_quote(basename($file, '.php'), '/') . '\s+extends\s+\w+/mi', $code, "$file has no class with the name of the file.");
        $this->assertStringContainsString('protected function before()', $code, "$file has no method before().");
    }

    #[DataProvider('pages')]
    public function testThePageHasATemplate(string $page): void
    {
        $file = self::TEMPLATE . '/pages/' . $page . '/default.php';

        $this->assertFileExists(Files::path($file));
        $this->assertTrue(self::existsWithCase($file), "$file exists only with another case of its letters.");
        $this->assertMatchesRegularExpression('/defined\(\s*[\'"]_JEXEC[\'"]\s*\)\s*or\s+die/', Files::read($file));
    }

    #[DataProvider('pages')]
    public function testThePageAsksForItsContent(string $page): void
    {
        $code = Files::read(self::TEMPLATE . '/pages/' . $page . '/default.php');

        // The content comes from a controller or a layout, or the page shows what the view prepared ($this->content).
        $this->assertMatchesRegularExpression('/->(?:execute|subRequest|subLayout)\(|echo \$this->\w+/', $code, "The page $page shows no content.");
    }

    public function testEveryPageTemplateIsInTheList(): void
    {
        $found = [];

        foreach (glob(Files::path(self::TEMPLATE . '/pages/*/*/default.php')) ?: [] as $path) {
            $found[] = basename(\dirname($path, 2)) . '/' . basename(\dirname($path));
        }

        sort($found);

        $listed = self::PAGES;

        sort($listed);

        $this->assertSame($listed, $found, 'The pages of the template and the list of pages in this test are not the same.');
    }

    public function testEveryPageIsAKunenaView(): void
    {
        $views = array_unique(array_map(static fn ($page) => explode('/', $page)[0], self::PAGES));

        foreach ($views as $view) {
            // A view of the forum has a folder with its controllers or a class for the view.
            $this->assertTrue(
                is_dir(Files::path('src/site/src/Controller/' . ucfirst($view))) || is_dir(Files::path('src/site/src/View/' . ucfirst($view))),
                "The view $view has no controller and no view class."
            );
        }
    }
}
