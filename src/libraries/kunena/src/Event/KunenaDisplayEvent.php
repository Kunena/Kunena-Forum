<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Event
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Libraries\Event;

\defined('_JEXEC') or die;

use Joomla\CMS\Event\AbstractImmutableEvent;
use Joomla\Registry\Registry;

/**
 * Class for onKunenaDisplay event.
 * Example:
 *  new KunenaDisplayEvent('com_example.view', ['type' => '', 'view' => '', 'params' => Registry]);
 *
 * @since  6.5.0
 */
class KunenaDisplayEvent extends AbstractImmutableEvent
{
    /**
     * Constructor.
     *
     * @param   string  $name       The event name.
     * @param   array   $arguments  The event arguments.
     *
     * @since   4.0.0
     * @throws  \BadMethodCallException
     */
    public function __construct(string $name, array $arguments = [])
    {
        parent::__construct($name, $arguments);

        if (!\array_key_exists('type', $this->arguments)) {
            throw new \BadMethodCallException("Argument 'type' of event {$name} is required but has not been provided");
        }
    }

    /**
     * Setter for the type argument.
     *
     * @param   string  $value  The value to set
     *
     * @return  string
     * @since   6.5.0
     */
    protected function onSetType(string $value): string
    {
        return $value;
    }

    /**
     * Getter for the type argument.
     *
     * @return  string
     * @since   6.5.0
     */
    public function getType(): string
    {
        return $this->arguments['type'];
    }

    /**
     * Setter for the type argument.
     *
     * @return  $this
     * @since   6.5.0
     */
    public function setType(string $type)
    {
        return $this->setArgument('type', $type);
    }

    /**
     * Setter for the view argument.
     *
     * @param   Object  $value  The value to set
     *
     * @return  Object
     * @since   6.5.0
     */
    protected function onSetView(Object $value): Object
    {
        return $value;
    }

    /**
     * Getter for the view argument.
     *
     * @return  Object
     * @since   6.5.0
     */
    public function getView(): Object
    {
        return $this->arguments['view'];
    }

    /**
     * Setter for the view argument.
     *
     * @return  $this
     * @since   6.5.0
     */
    public function setView(Object $view)
    {
        return $this->setArgument('view', $view);
    }

    /**
     * Setter for the params argument.
     *
     * @param   Registry  $value  The value to set
     *
     * @return  Registry
     * @since   6.5.0
     */
    protected function onSetParams(Registry $value): Registry
    {
        return $value;
    }

    /**
     * Getter for the params argument.
     *
     * @return  Registry
     * @since   6.5.0
     */
    public function getParams(): Registry
    {
        return $this->arguments['params'];
    }

    /**
     * Setter for the params argument.
     *
     * @return  $this
     * @since   6.5.0
     */
    public function setParams(string $params)
    {
        return $this->setArgument('params', $params);
    }
}
