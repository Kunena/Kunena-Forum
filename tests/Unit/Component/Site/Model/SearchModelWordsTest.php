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

namespace Kunena\Tests\Unit\Component\Site\Model;

use Joomla\CMS\MVC\Model\State;
use Kunena\Forum\Site\Model\SearchModel;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionProperty;

/**
 * The words of a search, and the day of "at a specific date".
 */
class SearchModelWordsTest extends UnitTestCase
{
    protected function setUp(): void
    {
        $this->requireJoomla();
    }

    public static function searches(): array
    {
        return [
            'two words with an apostrophe' => ["don't won't", ["don't", "won't"]],
            'French'                       => ["l'été d'accord", ["l'été", "d'accord"]],
            'a phrase in single quotes'    => ["'exact phrase' word", ['exact phrase', 'word']],
            'a phrase in double quotes'    => ['"two words" more', ['two words', 'more']],
            'a word and a phrase'          => ["it's 'a phrase'", ["it's", 'a phrase']],
            'commas'                       => ["forum,'b c',post", ['forum', 'b c', 'post']],
        ];
    }

    /**
     * An apostrophe in a word (l'été, don't) opened a phrase in single quotes: "don't won't" was searched as "don", "t won" and "t",
     * all of which a message must have, so the message with these words was not found.
     */
    #[DataProvider('searches')]
    public function testTheWordsOfASearch(string $search, array $words): void
    {
        $model = (new ReflectionClass(SearchModel::class))->newInstanceWithoutConstructor();
        $state = new State();
        $state->set('searchwords', $search);
        (new ReflectionProperty($model, 'state'))->setValue($model, $state);
        (new ReflectionProperty($model, '__state_set'))->setValue($model, true);

        $this->assertSame($words, $model->getSearchWords());
    }
}
