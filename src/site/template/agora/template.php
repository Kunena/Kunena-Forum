<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Agora
 * @subpackage      Template
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Path\KunenaPath;
use KunenaScssPhp\ScssPhp\Exception\SassException;

require_once KPATH_SITE . '/template/aurelia/template.php';

/**
 * Agora template: a modern look (cards, light and dark mode, mobile first) on top of Aurelia.
 *
 * Every layout and page which Agora does not have itself is taken from Aurelia, so Agora keeps all of its features.
 *
 * @since   Kunena 8.0
 */
class KunenaTemplateAgora extends KunenaTemplateAurelia
{
    /**
     * List of parent template names: the missing files are searched from Aurelia.
     *
     * @var array
     * @since   Kunena 8.0
     */
    protected mixed $default = ['aurelia'];

    /**
     * The scripts of Aurelia (assets/js) are used when Agora has no file of the same name.
     *
     * @param   string  $filename  filename
     * @param   array   $options   options
     * @param   array   $attribs   attributes
     *
     * @return  mixed
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function addScript(string $filename, $options = [], $attribs = [])
    {
        if (!str_starts_with($filename, 'assets/') || is_file(KPATH_SITE . "/template/{$this->name}/{$filename}")) {
            return parent::addScript($filename, $options, $attribs);
        }

        $name       = $this->name;
        $this->name = 'aurelia';

        try {
            return parent::addScript($filename, $options, $attribs);
        } finally {
            $this->name = $name;
        }
    }

    /**
     * The styles of Agora (option style), the look only: each one is a set of design tokens (colours, type, shapes, spaces) and a
     * few rules in assets/scss/styles/, chosen by the attribute data-agora-style of #kunena. Midnight has only dark colours.
     *
     * @var string[]
     * @since   Kunena 8.0
     */
    public const STYLES = ['default', 'soft', 'midnight', 'compact', 'conversation', 'nordic', 'tonal'];

    /**
     * The layouts of Agora (option layoutSet), the HTML only, independent of the style: each one replaces some layouts and pages
     * (layoutsets/<layout>/layouts, layoutsets/<layout>/pages) and has its rules in assets/scss/layoutsets/, chosen by the attribute
     * data-agora-layout of #kunena. Their rules use the tokens, so every layout goes with every style.
     *
     * @var string[]
     * @since   Kunena 8.0
     */
    public const LAYOUTS = ['default', 'cards', 'columns', 'dense', 'chat', 'profile', 'app'];

    /**
     * The accent colour of the options before a style was chosen: with this value a style keeps its own accent.
     *
     * @var string
     * @since   Kunena 8.0
     */
    public const DEFAULT_ACCENT = '#3d5afe';

    /**
     * The style chosen in the options of the template, default when the value is unknown.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public function getStyle(): string
    {
        $style = (string) $this->params->get('style', 'default');

        return \in_array($style, self::STYLES, true) ? $style : 'default';
    }

    /**
     * The layout chosen in the options of the template, default when the value is unknown.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public function getLayoutSet(): string
    {
        $layout = (string) $this->params->get('layoutSet', 'default');

        return \in_array($layout, self::LAYOUTS, true) ? $layout : 'default';
    }

    /**
     * The folders where a layout or a page is searched, the last one first: the folder of the chosen layout
     * (layoutsets/<layout>/layouts, layoutsets/<layout>/pages) comes before the one of Agora, so a layout replaces the files it
     * has; the overrides of the site template (html/com_kunena) still come before both.
     *
     * @param   string  $path      path inside the template
     * @param   bool    $fullpath  full paths
     *
     * @return  array
     *
     * @since   Kunena 8.0
     */
    public function getTemplatePaths($path = '', $fullpath = false): array
    {
        return $this->addLayoutSetPath(parent::getTemplatePaths($path, $fullpath), $path, ($fullpath ? KPATH_SITE : KPATH_COMPONENT_RELATIVE) . '/template');
    }

    /**
     * The folders of getTemplatePaths() with the one of the chosen layout right after the one of Agora (so it is searched before it).
     *
     * @param   string[]  $paths  the folders, the last one searched first
     * @param   string    $path   path inside the template
     * @param   string    $root   the folder of the templates in these paths
     *
     * @return  string[]
     *
     * @since   Kunena 8.0
     */
    public function addLayoutSetPath(array $paths, string $path, string $root): array
    {
        $layout = $this->getLayoutSet();

        if ($layout === 'default') {
            return $paths;
        }

        $clean = $path ? KunenaPath::clean("/$path") : '';
        $own   = "{$root}/{$this->name}";
        $index = array_search($own . $clean, $paths, true);

        if ($index === false) {
            return $paths;
        }

        array_splice($paths, $index + 1, 0, [$own . "/layoutsets/{$layout}" . $clean]);

        return $paths;
    }

    /**
     * The colour scheme chosen in the options of the template: auto (the colours of the site template around the forum), light or
     * dark. The style Midnight is always dark.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public function getColorScheme(): string
    {
        if ($this->getStyle() === 'midnight') {
            return 'dark';
        }

        $scheme = (string) $this->params->get('colorScheme', 'auto');

        return \in_array($scheme, ['auto', 'light', 'dark'], true) ? $scheme : 'auto';
    }

    /**
     * The attributes of the element #kunena for the style, the layout and the colour scheme of the options: the scheme of
     * Bootstrap, light without JavaScript when the scheme follows the site template.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public function getSchemeAttributes(): string
    {
        $style  = $this->getStyle();
        $layout = $this->getLayoutSet();
        $scheme = $this->getColorScheme();
        $theme  = $scheme === 'auto' ? 'light' : $scheme;

        return " data-agora-style=\"{$style}\" data-agora-layout=\"{$layout}\" data-agora-scheme=\"{$scheme}\" data-bs-theme=\"{$theme}\"";
    }

    /**
     * The rule which gives the forum the accent colour of the options, stronger than the accent of a style. Empty when the option
     * is not a colour, or when a style other than default is chosen and the accent is still the one of before (the style keeps its
     * own accent then).
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public function getAccentDeclaration(): string
    {
        $accent = (string) $this->params->get('accentColor');

        if (!preg_match('/^#[0-9a-f]{3,8}$/i', $accent)) {
            return '';
        }

        if ($this->getStyle() !== 'default' && strtolower($accent) === self::DEFAULT_ACCENT) {
            return '';
        }

        return "#kunena.layout.agora[data-agora-style][data-agora-scheme] { --agora-accent: {$accent}; }";
    }

    /**
     * A small script, put right in the element #kunena, which gives it the colours of the site template around the forum before the
     * page is shown when the scheme of the options is auto: the nearest data-bs-theme (light or dark) of an ancestor, else the first
     * background of an ancestor which is not transparent (dark when its relative luminance is below 0.5), else light. The choice is
     * kept as window.kunenaAgoraTheme(), which agora.js calls again when the colours of the page change.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public function getSchemeScript(): string
    {
        if ($this->getColorScheme() !== 'auto') {
            return '';
        }

        return '<script>(function (el) { try {'
            . ' window.kunenaAgoraTheme = function (forum) {'
            . ' var node, theme, rgb, i, lum;'
            . ' for (node = forum.parentElement; node; node = node.parentElement) {'
            . ' theme = node.getAttribute("data-bs-theme"); if (theme === "dark" || theme === "light") { return theme; } }'
            . ' for (node = forum.parentElement; node; node = node.parentElement) {'
            . ' rgb = (getComputedStyle(node).backgroundColor.match(/^rgba?\(([^)]*)\)/) || [])[1];'
            . ' rgb = rgb ? rgb.match(/[\d.]+/g) : null;'
            . ' if (!rgb || rgb.length < 3 || (rgb.length > 3 && +rgb[3] === 0)) { continue; }'
            . ' for (i = 0; i < 3; i++) { rgb[i] = rgb[i] / 255;'
            . ' rgb[i] = rgb[i] <= 0.03928 ? rgb[i] / 12.92 : Math.pow((rgb[i] + 0.055) / 1.055, 2.4); }'
            . ' lum = 0.2126 * rgb[0] + 0.7152 * rgb[1] + 0.0722 * rgb[2];'
            . ' return lum < 0.5 ? "dark" : "light"; }'
            . ' return "light"; };'
            . ' el.setAttribute("data-bs-theme", window.kunenaAgoraTheme(el)); } catch (e) {} })(document.currentScript.parentElement);</script>';
    }

    /**
     * Template initialization: the style sheet of Agora is compiled when one of its sources (or one of Aurelia) changed.
     *
     * @return  void
     *
     * @throws  Exception
     * @throws  SassException
     * @since   Kunena 8.0
     */
    public function initialize(): void
    {
        $this->compileScss("assets/scss/{$this->name}.scss", 'kunena.css');

        $custom = KPATH_SITE . "/template/{$this->name}/assets/scss/custom.scss";

        if (is_file($custom) && filesize($custom)) {
            $this->compileScss('assets/scss/custom.scss', 'kunena-custom.css');
        }

        parent::initialize();

        // The colours of the scheme for the editor, and for the scheme auto the colours of the site template when they change.
        $this->addScript('assets/js/agora.js');

        $accent = $this->getAccentDeclaration();

        if ($accent !== '') {
            $this->addStyleDeclaration($accent);
        }
    }

    /**
     * The language strings of Aurelia and of Agora.
     *
     * @return  void
     *
     * @throws  Exception
     * @since   Kunena 8.0
     */
    public function loadLanguage(): void
    {
        parent::loadLanguage();

        KunenaFactory::loadLanguage('kunena_tmpl_agora');
    }

    /**
     * The style sheet is compiled again when a source of Agora or a source of Aurelia (which it imports) is newer.
     *
     * @param   string  $inputFile   input
     * @param   string  $outputPath  output
     *
     * @return  boolean
     *
     * @since   Kunena 8.0
     */
    protected function shouldCompileScss(string $inputFile, string $outputPath): bool
    {
        if (parent::shouldCompileScss($inputFile, $outputPath)) {
            return true;
        }

        return $this->getMaxScssMtimeCached(KPATH_SITE . '/template/aurelia/assets/scss') > $this->getPathStatCached($outputPath)['mtime'];
    }
}
