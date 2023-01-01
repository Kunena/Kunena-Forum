<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Topic
 *
 * @copyright       Copyright (C) 2008 - 2023 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Topic\Edit;

\defined('_JEXEC') or die;

use Exception;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Template\KunenaTemplate;

/**
 * KunenaLayoutTopicEditEditor
 *
 * @since   Kunena 4.0
 */
class TopicEditEditor extends KunenaLayout
{
    /**
     * @var     KunenaConfig
     * @since   Kunena 6.0
     */
    public $config;

    /**
     * @var     KunenaTemplate
     * @since   Kunena 6.0
     */
    public $ktemplate;

    /**
     * Define javascript variables to show or disable some bbcode buttons
     *
     * @return  void
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function getBBcodesEnabled()
    {
        $this->ktemplate  = KunenaFactory::getTemplate();
        $templatesettings = $this->ktemplate->params;

        $bbcodes = [
            "spoiler",
            "maps",
            "twitter",
            "link",
            "picture",
            "hide",
            "table",
            "code",
            "quote",
            "divider",
            "instagram",
            "soundcloud",
            "confidential",
            "hr",
            "listitem",
            "supscript",
            "subscript",
            "numericlist",
            "bulletedlist",
            "alignright",
            "alignleft",
            "center",
            "underline",
            "italic",
            "bold",
            "strikethrough",
            "colors",
            "size",
            "video",
            "emoticons",
            "ebay",
        ];

        foreach ($bbcodes as $item) {
            $option = 0;

            if ($item == 'video' || $item == 'ebay') {
                $tag = "show" . $item . "tag";

                if ($this->config->$tag && $templatesettings->get($item)) {
                    $option = 1;
                }
            } elseif ($item == 'emoticons') {
                if (!$this->config->disableEmoticons && $templatesettings->get($item)) {
                    $option = 1;
                }
            } elseif ($templatesettings->get($item)) {
                $option = 1;
            }

            $this->addScriptOptions("kunena_show" . $item . "tag", $option);
        }
    }
}
