<?php

/**
 * Kunena Component
 *
 *  - view=ajax&display=Topic&format=json&catid=N returned the template text of a category (the text that new topics start with) without
 *    asking whether the visitor may read the category: the same gap as the task categorytemplate, which was closed before.
 *  - the answer of the ajax view for an unknown page named the class that it had looked for, a name made from the request.
 *
 * @package         Kunena.Tests
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 **/

namespace Kunena\Forum\Libraries\Controller {
    if (!class_exists(KunenaControllerDisplay::class, false)) {
        class KunenaControllerDisplay
        {
            public $input;

            public function __construct($input = null)
            {
                $this->input = $input;
            }
        }
    }
}

namespace Kunena\Forum\Libraries\Forum\Category {
    if (!class_exists(KunenaCategoryHelper::class, false)) {
        class KunenaCategoryHelper
        {
            /** @var array<int, object> */
            public static $categories = [];

            public static function get($id)
            {
                return self::$categories[$id] ?? new class () {
                    public $topictemplate = 'SECRET TEMPLATE OF A CATEGORY THAT DOES NOT EXIST';

                    public function tryAuthorise($action)
                    {
                        throw new \RuntimeException('Not found', 404);
                    }
                };
            }

            public static function make(string $template, bool $readable): object
            {
                return new class ($template, $readable) {
                    public function __construct(public string $topictemplate, private bool $readable)
                    {
                    }

                    public function tryAuthorise($action)
                    {
                        if ($action !== 'read' || !$this->readable) {
                            throw new \RuntimeException('No access', 403);
                        }
                    }
                };
            }
        }
    }
}

namespace Kunena\Tests\Unit\Controllers {
    use Kunena\Forum\Libraries\Forum\Category\KunenaCategoryHelper;
    use Kunena\Forum\Libraries\Request\KunenaRequest;
    use PHPUnit\Framework\Attributes\DataProvider;
    use PHPUnit\Framework\TestCase;

    class TopicTemplateAjaxTest extends TestCase
    {
        private const DISPLAY = __DIR__ . '/../../../src/site/src/Controller/Topic/Json/TopicJsonDisplay.php';
        private const REQUEST = __DIR__ . '/../../../src/libraries/kunena/src/Request/KunenaRequest.php';

        public static function setUpBeforeClass(): void
        {
            if (!\defined('_JEXEC')) {
                \define('_JEXEC', 1);
            }

            require_once self::DISPLAY;
            require_once self::REQUEST;
        }

        protected function setUp(): void
        {
            KunenaCategoryHelper::$categories = [];
        }

        private function answer(int $catid): string
        {
            $class   = 'Kunena\Forum\Site\Controller\Topic\Json\TopicJsonDisplay';
            $input   = new class ($catid) {
                public function __construct(private int $catid)
                {
                }

                public function getInt($name, $default = 0)
                {
                    return $name === 'catid' ? $this->catid : $default;
                }
            };
            $display = new $class($input);

            $before = new \ReflectionMethod($display, 'before');
            $before->invoke($display);

            return (string) $display->response;
        }

        public function testReaderGetsTheTemplate(): void
        {
            KunenaCategoryHelper::$categories[5] = KunenaCategoryHelper::make('Please describe the problem', true);

            $this->assertSame('Please describe the problem', $this->answer(5));
        }

        public function testNobodyElseGetsTheTemplateOfAPrivateCategory(): void
        {
            KunenaCategoryHelper::$categories[6] = KunenaCategoryHelper::make('Internal: the salary plans of the staff', false);

            $this->assertSame('', $this->answer(6));
        }

        public function testCategoryThatDoesNotExistGivesNothing(): void
        {
            $this->assertSame('', $this->answer(99999));
            $this->assertSame('', $this->answer(0));
        }

        public function testSourceAsksBeforeItUsesTheTemplate(): void
        {
            $source = file_get_contents(self::DISPLAY);

            $this->assertIsInt(strpos($source, "tryAuthorise('read')"));
            $this->assertLessThan(strpos($source, '->topictemplate'), strpos($source, "tryAuthorise('read')"), 'The template is read after the check.');
        }

        public static function requests(): array
        {
            return [
                ['Undefined/Json/Display'],
                ['Topic/Raw/Display'],
                ['Admin.Secret/Json/Display'],
                ['../../etc/passwd/Json/Display'],
                ["Topic'; DROP TABLE x;--/Json/Display"],
                ['<script>alert(1)</script>/Json/Display'],
            ];
        }

        #[DataProvider('requests')]
        public function testUnknownPageDoesNotNameInternalClasses(string $path): void
        {
            try {
                KunenaRequest::factory($path);
                $this->fail('A controller was created for ' . $path);
            } catch (\InvalidArgumentException $e) {
                $this->assertSame(404, $e->getCode());
                $this->assertStringNotContainsString('Kunena', $e->getMessage());
                $this->assertStringNotContainsString('Controller', $e->getMessage());
                $this->assertStringNotContainsString('\\', $e->getMessage());
                $this->assertStringNotContainsString(basename($path), $e->getMessage());
            }
        }

        public function testEmptyRequestStillSaysSo(): void
        {
            $this->expectException(\InvalidArgumentException::class);
            $this->expectExceptionCode(404);

            KunenaRequest::factory('///');
        }
    }
}
