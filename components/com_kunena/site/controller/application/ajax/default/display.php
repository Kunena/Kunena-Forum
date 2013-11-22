<?php
/**
 * Kunena Component
 * @package     Kunena.Site
 * @subpackage  Controller.Application
 *
 * @copyright   (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

/**
 * Class ComponentKunenaControllerApplicationAjaxDefaultDisplay
 *
 * @since  3.1
 */
class ComponentKunenaControllerApplicationAjaxDefaultDisplay extends KunenaControllerApplicationDisplay
{
	/**
	 * Return true if layout exists.
	 *
	 * @return bool
	 */
	public function exists()
	{
		return KunenaFactory::getTemplate()->isHmvc();
	}

	/**
	 * Return AJAX for the requested layout.
	 *
	 * @return string  String in JSON or RAW.
	 *
	 * @throws RuntimeException
	 * @throws KunenaExceptionAuthorise
	 */
	public function execute()
	{
		$format = $this->input->getWord('format', 'html');
		$function = 'display' . ucfirst($format);

		if (!method_exists($this, $function))
		{
			// Invalid page request.
			throw new RuntimeException(JText::_('COM_KUNENA_NO_ACCESS'), 404);
		}

		// Run before executing action.
		$result = $this->before();

		if ($result === false)
		{
			$content = new RuntimeException(JText::_('COM_KUNENA_NO_ACCESS'), 404);
		}
		elseif (!JSession::checkToken())
		{
			// Invalid access token.
			$content = new RuntimeException(JText::_('COM_KUNENA_ERROR_TOKEN'), 401);
		}
		elseif ($this->config->board_offline && !$this->me->isAdmin())
		{
			// Forum is offline.
			$content = new RuntimeException(JText::_('COM_KUNENA_FORUM_IS_OFFLINE'), 503);
		}
		elseif ($this->config->regonly && !$this->me->exists())
		{
			// Forum is for registered users only.
			$content = new RuntimeException(JText::_('COM_KUNENA_LOGIN_NOTIFICATION'), 403);
		}
		else
		{
			$display = $this->input->getCmd('display', 'Undefined') . '/Display';

			try
			{
				$content = KunenaRequest::factory($display, $this->input, $this->options)
					->setPrimary()->execute()->render();
			}
			catch (Exception $e)
			{
				$content = $e;
			}
		}

		return $this->$function($content);
	}

	/**
	 * Prepare AJAX display.
	 *
	 * @return void
	 */
	protected function before()
	{
		// Load language files.
		KunenaFactory::loadLanguage('com_kunena.sys', 'admin');
		KunenaFactory::loadLanguage('com_kunena.templates');
		KunenaFactory::loadLanguage('com_kunena.models');
		KunenaFactory::loadLanguage('com_kunena.views');

		$this->me = KunenaUserHelper::getMyself();
		$this->config = KunenaConfig::getInstance();
		$this->document = JFactory::getDocument();
		$this->template = KunenaFactory::getTemplate();
		$this->template->initialize();
	}

	/**
	 * Display output as RAW.
	 *
	 * @param   mixed  $content  Content to be returned.
	 *
	 * @return  string
	 */
	public function displayRaw($content)
	{
		if ($content instanceof Exception)
		{
			$this->setResponseStatus($content->getCode());

			return $content->getCode() . ' ' . $content->getMessage();
		}

		return (string) $content;
	}

	/**
	 * Display output as JSON.
	 *
	 * @param   mixed  $content  Content to be returned.
	 *
	 * @return  string
	 */
	public function displayJson($content)
	{
		// TODO: Joomla 3.1+ uses JResponseJson (we just emulate it for now).
		$response = new StdClass;
		$response->success = true;
		$response->message = null;
		$response->messages = null;
		$response->data = null;

		if ($content instanceof Exception)
		{
			// Build data from exceptions.
			$exceptions = array();
			$e = $content;

			do
			{
				$exception = array(
					'code' => $e->getCode(),
					'message' => $e->getMessage()
				);

				if (JDEBUG)
				{
					$exception += array(
						'type' => get_class($e),
						'file' => $e->getFile(),
						'line' => $e->getLine()
					);
				}

				$exceptions[] = $exception;
				$e = $e->getPrevious();
			}
			while (JDEBUG && $e);

			// Create response.
			$response->success = false;
			$response->message = $content->getcode() . ' ' . $content->getMessage();
			$response->data = array('exceptions' => $exceptions);
		}
		else
		{
			$response->data = (string) $content;
		}

		$messages = $this->app->getMessageQueue();

		// Build the sorted messages list
		if (is_array($messages) && count($messages))
		{
			foreach ($messages as $message)
			{
				if (isset($message['type']) && isset($message['message']))
				{
					$lists[$message['type']][] = $message['message'];
				}
			}
		}

		// If messages exist add them to the output
		if (isset($lists) && is_array($lists))
		{
			$response->messages = $lists;
		}

		return json_encode($response);
	}
}
