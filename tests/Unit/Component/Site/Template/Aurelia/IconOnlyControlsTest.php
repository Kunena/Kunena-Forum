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

namespace Kunena\Tests\Unit\Component\Site\Template\Aurelia;

use Kunena\Tests\Unit\Support\Files;
use Kunena\Tests\Unit\UnitTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * The links and buttons whose only content is an icon (the icons are aria-hidden) have an accessible name: an aria-label built
 * from a language key, in Aurelia and in the layouts Agora overrides.
 */
class IconOnlyControlsTest extends UnitTestCase
{
    private const AURELIA = 'src/site/template/aurelia/layouts/';

    private const AGORA = 'src/site/template/agora/layouts/';

    /**
     * @return  array<string, array{string, string, string}>  The layout, a text inside the control and the expected key.
     */
    public static function controls(): array
    {
        $controls = [
            'aurelia section pencil' => [self::AURELIA . 'category/index/default.php', "(int) \$section->id . '&layout=manage", "Text::sprintf('COM_KUNENA_CATEGORY_EDIT_NAMED', \$section->name)"],
            'aurelia section toggle' => [self::AURELIA . 'category/index/default.php', 'data-bs-target="#section<?php echo $section->id', "Text::sprintf('COM_KUNENA_TOGGLE_NAMED', \$section->name)"],
            'aurelia user e-mail'    => [self::AURELIA . 'user/item/summary/default.php', 'href="mailto:', "Text::_('COM_KUNENA_MYPROFILE_EMAIL')"],
            'aurelia user delete'    => [self::AURELIA . 'user/attachments/default.php', 'href="#modaldelete<?php echo $i ?>"', "\$this->escape(Text::sprintf('COM_KUNENA_FILES_DELETE_NAMED', \$attachment->getFilename(false)))"],
            'aurelia category top'   => [self::AURELIA . 'category/item/default.php', 'href="#forumtop"', "Text::_('COM_KUNENA_GEN_GOTOTOP')"],
            'aurelia message top'    => [self::AURELIA . 'message/list/default.php', 'href="#forumtop"', "Text::_('COM_KUNENA_GEN_GOTOTOP')"],
            'aurelia topic top'      => [self::AURELIA . 'topic/list/default.php', 'href="#forumtop"', "Text::_('COM_KUNENA_GEN_GOTOTOP')"],
            'aurelia writeaccess'    => [self::AURELIA . 'widget/writeaccess/default.php', 'data-bs-toggle="collapse"', "Text::sprintf('COM_KUNENA_TOGGLE_NAMED', Text::_('COM_KUNENA_WRITEACCESS'))"],
            'aurelia statistics'     => [self::AURELIA . 'widget/statistics/default.php', 'data-bs-toggle="collapse"', "Text::sprintf('COM_KUNENA_TOGGLE_NAMED', Text::_('COM_KUNENA_STATISTICS'))"],
            'aurelia whoisonline'    => [self::AURELIA . 'widget/whoisonline/default.php', 'data-bs-toggle="collapse"', "Text::sprintf('COM_KUNENA_TOGGLE_NAMED', Text::_('COM_KUNENA_MEMBERS'))"],
        ];

        foreach (['aurelia' => self::AURELIA, 'agora' => self::AGORA] as $template => $folder) {
            $controls[$template . ' category pencil']    = [$folder . 'category/index/default.php', "(int) \$category->id . '&layout=manage", "Text::sprintf('COM_KUNENA_CATEGORY_EDIT_NAMED', \$category->name)"];
            $controls[$template . ' subcategory pencil'] = [$folder . 'category/index/default.php', "(int) \$subcategory->id . '&layout=manage", "htmlspecialchars(Text::sprintf('COM_KUNENA_CATEGORY_EDIT_NAMED', \$subcategory->name), ENT_COMPAT, 'UTF-8')"];
            $controls[$template . ' category rss']       = [$folder . 'category/index/default.php', 'getCategoryRSSURL($category->id)', "Text::_('COM_KUNENA_CATEGORIES_LABEL_GETRSS')"];
            $controls[$template . ' user website']       = [$folder . 'user/item/summary/default.php', 'KunenaIcons::globe(); ?></a>', "Text::_('COM_KUNENA_MYPROFILE_WEBSITE')"];
        }

        return $controls;
    }

    #[DataProvider('controls')]
    public function testTheControlHasAnAriaLabel(string $layout, string $needle, string $label): void
    {
        $control = $this->control(Files::read($layout), $needle, $layout);

        $this->assertStringContainsString('aria-label="', $control, $layout . ': the control has no aria-label.');
        $this->assertStringContainsString($label, $control, $layout . ': the aria-label is not built from the expected key.');
    }

    /**
     * getFilename() escapes by default: the label passes the raw name, so the whole value is escaped only once.
     */
    public function testTheDeleteLabelEscapesTheFileNameOnce(): void
    {
        $layout  = self::AURELIA . 'user/attachments/default.php';
        $control = $this->control(Files::read($layout), 'href="#modaldelete<?php echo $i ?>"', $layout);

        $this->assertStringNotContainsString('$attachment->getFilename()', $control, $layout . ': the file name is escaped twice.');
    }

    public function testEveryPencilOfTheCategoryIndexHasAName(): void
    {
        foreach ([self::AURELIA => 3, self::AGORA => 3] as $folder => $count) {
            $lines = preg_grep('/KunenaIcons::pencil\(\)/', explode("\n", Files::read($folder . 'category/index/default.php')));

            $this->assertCount($count, $lines, $folder . ': unexpected number of pencils.');

            foreach ($lines as $line) {
                $this->assertStringContainsString('aria-label', $line);
            }
        }

        foreach (preg_grep('/KunenaIcons::pencil\(\)/', explode("\n", Files::read(self::AURELIA . 'category/index/default.php'))) as $line) {
            $this->assertStringContainsString('COM_KUNENA_CATEGORY_EDIT_NAMED', $line);
        }
    }

    public function testEveryGoToTopLinkHasAName(): void
    {
        foreach (['category/item', 'message/list', 'topic/list', 'user/list'] as $layout) {
            $content = Files::read(self::AURELIA . $layout . '/default.php');

            $this->assertSame(1, substr_count($content, 'href="#forumtop"'), $layout . ': one go to top link expected.');
            $this->assertStringContainsString('COM_KUNENA_GEN_GOTOTOP', $this->control($content, 'href="#forumtop"', $layout));
        }
    }

    public function testTheWidgetTogglesAreButtons(): void
    {
        foreach (['writeaccess' => 'writeaccess', 'statistics' => 'kstats', 'whoisonline' => 'kwho'] as $widget => $target) {
            $layout  = self::AURELIA . 'widget/' . $widget . '/default.php';
            $content = Files::read($layout);
            $control = $this->control($content, 'data-bs-toggle="collapse"', $layout);

            $this->assertStringNotContainsString('<div class="btn ', $content, $layout);
            $this->assertStringStartsWith('<button type="button"', $control, $layout);
            $this->assertStringContainsString('data-bs-target="#' . $target . '"', $control, $layout);
            $this->assertStringContainsString('aria-controls="' . $target . '"', $control, $layout);
            $this->assertStringContainsString('aria-expanded="true"', $control, $layout);
            $this->assertStringContainsString('id="' . $target . '"', $content, $layout);
            $this->assertStringContainsString('use Joomla\CMS\Language\Text;', $content, $layout);
        }
    }

    public function testTheNewKeysHaveAnEnglishText(): void
    {
        $strings = Files::languageFolder('src/site/language/en-GB');

        foreach (['COM_KUNENA_CATEGORY_EDIT_NAMED', 'COM_KUNENA_FILES_DELETE_NAMED', 'COM_KUNENA_TOGGLE_NAMED'] as $key) {
            $this->assertArrayHasKey($key, $strings);
            $this->assertStringContainsString('%s', $strings[$key], $key . ' has no %s for the name.');
        }
    }

    /**
     * The markup of the link or button around the first occurrence of a text, from its opening tag to its closing tag.
     */
    private function control(string $content, string $needle, string $layout): string
    {
        $position = strpos($content, $needle);

        $this->assertNotFalse($position, $layout . ' does not contain ' . $needle);

        $before = substr($content, 0, $position);
        $link   = strrpos($before, '<a ');
        $button = strrpos($before, '<button');
        $start  = max((int) $link, (int) $button);
        $close  = $start === $button ? '</button>' : '</a>';
        $end    = strpos($content, $close, $position);

        $this->assertNotFalse($end, $layout . ': the control around ' . $needle . ' is not closed.');

        return substr($content, $start, $end + \strlen($close) - $start);
    }
}
