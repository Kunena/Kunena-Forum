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
use ScssPhp\ScssPhp\Exception\SassException;

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
     * The colour scheme chosen in the options of the template: auto (as the device), light or dark.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public function getColorScheme(): string
    {
        $scheme = (string) $this->params->get('colorScheme', 'auto');

        return \in_array($scheme, ['auto', 'light', 'dark'], true) ? $scheme : 'auto';
    }

    /**
     * The attributes of the element #kunena for the colour scheme of the options: the scheme of Bootstrap, light without JavaScript
     * when the scheme follows the device.
     *
     * @return  string
     *
     * @since   Kunena 8.0
     */
    public function getSchemeAttributes(): string
    {
        $scheme = $this->getColorScheme();
        $theme  = $scheme === 'auto' ? 'light' : $scheme;

        return " data-agora-scheme=\"{$scheme}\" data-bs-theme=\"{$theme}\"";
    }

    /**
     * A small script, put right in the element #kunena, which gives it the colours of the device before the page is shown when the
     * scheme of the options follows the device (no flash of the light colours on a dark device).
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

        return '<script>(function (el) { try { el.setAttribute("data-bs-theme",'
            . ' window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"); } catch (e) {} })(document.currentScript.parentElement);</script>';
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

        // The colours follow the device: the script changes them when the device does.
        if ($this->getColorScheme() === 'auto') {
            $this->addScript('assets/js/agora.js');
        }

        $accent = (string) $this->params->get('accentColor');

        if (preg_match('/^#[0-9a-f]{3,8}$/i', $accent)) {
            $this->addStyleDeclaration("#kunena.layout.agora { --agora-accent: {$accent}; }");
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
