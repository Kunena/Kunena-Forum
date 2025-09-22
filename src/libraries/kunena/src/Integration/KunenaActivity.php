<?php

/**
 * Kunena Component
 *
 * @package       Kunena.Framework
 * @subpackage    Integration
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license       https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link          https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Integration;

\defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Event\KunenaGetActivityEvent;

/**
 * Class KunenaIntegrationActivity
 *
 * @since 3.0.4
 */
class KunenaActivity
{
    /**
     * @var     ?Registry
     * @since   Kunena 7.0
     */
    protected ?Registry $params = \null;

    /**
     * @var     boolean
     * @since   Kunena 6.0
     */
    protected static $instance = false;

    /**
     * @var     array| KunenaActivity[]
     * @since   Kunena 6.0
     */
    protected $instances = [];

    /**
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public function __construct()
    {
        PluginHelper::importPlugin('kunena');

        $activityEvent = new KunenaGetActivityEvent('onKunenaGetActivity');
        Factory::getApplication()->getDispatcher()->dispatch('onKunenaGetActivity', $activityEvent);

        $classes = $activityEvent->getArgument('result', []);

        foreach ($classes as $class) {
            if ($class instanceof KunenaActivity) {
                $this->instances[] = $class;
            }
        }
    }

    /**
     * @return  static
     *
     * @since   Kunena 6.0
     *
     * @throws  Exception
     */
    public static function getInstance()
    {
        if (self::$instance === false) {
            self::$instance = new KunenaActivity();
        }

        return self::$instance;
    }

    /**
     * Method magical to call the right method in plugin integration
     *
     * @param   string  $method     Name of method to call
     * @param   array   $arguments  Arguments need to be passed to the method
     *
     * @return  mixed
     *
     * @since   Kunena 6.0
     */
    public function __call(string $method, array $arguments)
    {
        $ret = null;

        foreach ($this->instances as $instance) {
            if (method_exists($instance, $method)) {
                $r = \call_user_func_array([$instance, $method], $arguments);

                if ($r !== null && $ret === null) {
                    $ret = $r;
                }
            }
        }

        return $ret;
    }
}
