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

use Kunena\Forum\Libraries\BBCode\KunenaBBCodeLibrary;
use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use Nbbc\BBCode;

/**
 * Text in double brackets ([[1,2],[3,4]], [[Main Page]]) stays text. The tags of Kunena kept the rule "wiki" of nbbc, and Kunena has no
 * wiki: such a text became a link to "/?page=..." (the root of the domain, wrong for a site in a folder) and its brackets and commas
 * were mangled.
 */
class KunenaBBCodeDoubleBracketsTest extends UnitTestCase
{
    private const BBCODE = 'src/libraries/kunena/src/BBCode/KunenaBBCode.php';

    protected function setUp(): void
    {
        spl_autoload_register(static function ($class) {
            if (strpos($class, 'Nbbc\\') === 0) {
                $file = Files::path('src/libraries/kunena/External/Nbbc/src/' . substr($class, 5) . '.php');

                if (is_file($file)) {
                    require_once $file;
                }
            }
        });

        if (!\defined('KPATH_FRAMEWORK')) {
            \define('KPATH_FRAMEWORK', Files::path('src/libraries/kunena'));
        }

        if (!class_exists(KunenaBBCodeLibrary::class, false)) {
            require_once Files::path(self::BBCODE);
        }
    }

    /**
     * A parser with all the tags of Kunena, as KunenaBBCode has them.
     */
    private function parse(string $text): string
    {
        $parser = new BBCode();
        $parser->SetDetectURLs(false);
        $parser->SetEnableSmileys(false);
        $parser->ClearRules();

        foreach (self::bare(KunenaBBCodeLibrary::class)->default_tag_rules as $name => $rule) {
            $parser->AddRule($name, $rule);
        }

        return (string) $parser->Parse($text);
    }

    public function testDoubleBracketsStayText(): void
    {
        $this->assertSame(
            ['Matrix [[1,2],[3,4]] done', 'See [[Main Page]].'],
            [$this->parse('Matrix [[1,2],[3,4]] done'), $this->parse('See [[Main Page]].')]
        );
    }
}
