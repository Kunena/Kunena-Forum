<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Framework
 * @subpackage      File
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https: //www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https: //www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Form\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\PredefinedlistField;

/**
 * EbayLanguageField
 *
 * @since 7.0.0
 */
class EbayLanguageField extends PredefinedlistField
{
    /**
     * The form field type.
     *
     * @var    string
     */
    public $type = 'EbayLanguage';

    /**
     * Available predefined options
     *
     * @var  array<string>
     */
    protected $predefinedOptions = [
        '0'   => 'en-US',
        '2'   => 'en-CA',
        '3'   => 'en-GB',
        '15'  => 'en-AU',
        '16'  => 'de-AT',
        '23'  => 'fr-BE',
        '71'  => 'fr-FR',
        '77'  => 'de-DE',
        '101' => 'it-IT',
        '123' => 'nl-BE',
        '146' => 'nl-NL',
        '186' => 'es-ES',
        '193' => 'ch-CH',
        '201' => 'hk-HK',
        '203' => 'in-IN',
        '205' => 'ie-IE',
        '207' => 'my-MY',
        '210' => 'fr-CA',
        '211' => 'ph-PH',
        '212' => 'pl-PL',
        '216' => 'sg-SG',
    ];
}
