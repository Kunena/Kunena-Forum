<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Administrator
 * @subpackage      Views
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Administrator\View\User;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Utilities\ArrayHelper;
use Kunena\Forum\Libraries\Config\KunenaConfig;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Integration\KunenaAvatar;
use StdClass;
use Joomla\CMS\Factory;

/**
 * User view for Kunena backend
 *
 * @since  K1.0
 */
class HtmlView extends BaseHtmlView
{
	/**
	 * @var mixed
	 * @since version
	 */
	protected $config;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $avatar;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $editavatar;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $maxSig = '';

	/**
	 * @var mixed
	 * @since version
	 */
	protected $maxPersonalText = '';

	/**
	 * @var mixed
	 * @since version
	 */
	protected $social;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $selectMod;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $sub;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $ipslist;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $selectOrder;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $settings;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $selectRank;

	/**
	 * @var mixed
	 * @since version
	 */
	protected $modCats;

	/**
	 * @param   null  $tpl  tpl
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$userids = (array) Factory::getApplication()->getUserState('kunena.usermove.userids');

		if ($userids && $this->getLayout() == 'move')
		{
			$this->setToolBarMove();
			$this->catsList = $this->get('moveCatsList');
			$this->users    = $this->get('moveUser');

			return parent::display($tpl);
		}

		$this->user         = $this->get('user');
		$this->sub          = $this->get('subscriptions');
		$this->subsCatsList = $this->get('catSubscriptions');
		$this->ipslist      = $this->get('IPlist');

		$avatarIntegration = KunenaFactory::getAvatarIntegration();
		$this->editavatar  = ($avatarIntegration instanceof KunenaAvatar) && $this->user->avatar;
		$this->avatar      = $this->user->getAvatarImage(KunenaFactory::getTemplate()->params->get('avatarType'), 'thumb');

		// Make the select list for the moderator flag
		$yesnoMod [] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_YES'));
		$yesnoMod [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_NO'));

		// Build the html select list
		$this->selectMod = HTMLHelper::_('select.genericlist', $yesnoMod, 'moderator', 'class="inputbox form-control" size="2"', 'value', 'text', $this->user->moderator);

		// Make the select list for the moderator flag
		$yesnoOrder [] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_USER_ORDER_ASC'));
		$yesnoOrder [] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_USER_ORDER_DESC'));

		// Build the html select list
		$item             = new StdClass;
		$item->name       = 'hidemail';
		$item->label      = Text::_('COM_KUNENA_USER_HIDEEMAIL');
		$options          = [];
		$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
		$options[]        = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));
		$options[]        = HTMLHelper::_('select.option', 2, Text::_('COM_KUNENA_A_ONLY_REGISTERED'));
		$item->field      = HTMLHelper::_(
			'select.genericlist',
			$options,
			'hidemail',
			'class="kinputbox form-control" size="1"',
			'value',
			'text',
			$this->escape($this->user->hideEmail),
			'khidemail'
		);
		$this->settings[] = $item;

		$item             = new StdClass;
		$item->name       = 'showonline';
		$item->label      = Text::_('COM_KUNENA_USER_SHOWONLINE');
		$options          = [];
		$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
		$options[]        = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));
		$item->field      = HTMLHelper::_(
			'select.genericlist',
			$options,
			'showonline',
			'class="kinputbox form-control" size="1"',
			'value',
			'text',
			$this->escape($this->user->showOnline),
			'kshowonline'
		);
		$this->settings[] = $item;

		$item             = new StdClass;
		$item->name       = 'cansubscribe';
		$item->label      = Text::_('COM_KUNENA_USER_CANSUBSCRIBE');
		$options          = [];
		$options[]        = HTMLHelper::_('select.option', -1, Text::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
		$options[]        = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));
		$item->field      = HTMLHelper::_(
			'select.genericlist',
			$options,
			'cansubscribe',
			'class="kinputbox form-control" size="1"',
			'value',
			'text',
			$this->escape($this->user->canSubscribe),
			'kcansubscribe'
		);
		$this->settings[] = $item;

		$item             = new StdClass;
		$item->name       = 'userlisttime';
		$item->label      = Text::_('COM_KUNENA_USER_USERLISTTIME');
		$options          = [];
		$options[]        = HTMLHelper::_('select.option', -2, Text::_('COM_KUNENA_USER_ORDER_KUNENA_GLOBAL'));
		$options[]        = HTMLHelper::_('select.option', -1, Text::_('COM_KUNENA_SHOW_ALL'));
		$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_SHOW_LASTVISIT'));
		$options[]        = HTMLHelper::_('select.option', 4, Text::_('COM_KUNENA_SHOW_4_HOURS'));
		$options[]        = HTMLHelper::_('select.option', 8, Text::_('COM_KUNENA_SHOW_8_HOURS'));
		$options[]        = HTMLHelper::_('select.option', 12, Text::_('COM_KUNENA_SHOW_12_HOURS'));
		$options[]        = HTMLHelper::_('select.option', 24, Text::_('COM_KUNENA_SHOW_24_HOURS'));
		$options[]        = HTMLHelper::_('select.option', 48, Text::_('COM_KUNENA_SHOW_48_HOURS'));
		$options[]        = HTMLHelper::_('select.option', 168, Text::_('COM_KUNENA_SHOW_WEEK'));
		$options[]        = HTMLHelper::_('select.option', 720, Text::_('COM_KUNENA_SHOW_MONTH'));
		$options[]        = HTMLHelper::_('select.option', 8760, Text::_('COM_KUNENA_SHOW_YEAR'));
		$item->field      = HTMLHelper::_(
			'select.genericlist',
			$options,
			'userlisttime',
			'class="kinputbox form-control" size="1"',
			'value',
			'text',
			$this->escape($this->user->userListtime),
			'kuserlisttime'
		);
		$this->settings[] = $item;

		$ktemplate = KunenaFactory::getTemplate();
		$social    = $ktemplate->params->get('socialshare');

		if ($social != 0)
		{
			$item             = new StdClass;
			$item->name       = 'socialshare';
			$item->label      = Text::_('COM_KUNENA_USER_SOCIALSHARE');
			$options          = [];
			$options[]        = HTMLHelper::_('select.option', 0, Text::_('COM_KUNENA_NO'));
			$options[]        = HTMLHelper::_('select.option', 1, Text::_('COM_KUNENA_YES'));
			$item->field      = HTMLHelper::_(
				'select.genericlist',
				$options,
				'social',
				'class="kinputbox form-control" size="1"',
				'value',
				'text',
				$this->escape($this->user->socialshare),
				'ksocialshare'
			);
			$this->settings[] = $item;
		}

		$this->selectOrder = HTMLHelper::_('select.genericlist', $yesnoOrder, 'newOrder', 'class="inputbox form-control" size="2"', 'value', 'text', $this->user->ordering);
		$this->modCats     = $this->get('listmodcats');
		$this->selectRank  = $this->get('listuserranks');
		$this->social      = $this->user->socialButtons();
		$this->social      = ArrayHelper::toObject($this->social);

		$this->config = KunenaConfig::getInstance();

		$this->addToolbar();

		return parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function addToolbar(): void
	{
		// Set the title bar text
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'users');
		ToolbarHelper::spacer();
		ToolbarHelper::apply('user.apply');
		ToolbarHelper::spacer();
		ToolbarHelper::save('user.save');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel('cancel', 'COM_KUNENA_CANCEL');
		ToolbarHelper::spacer();
		$helpUrl = 'https://docs.kunena.org/en/manual/backend/users/edit-user';
		ToolbarHelper::help('COM_KUNENA', false, $helpUrl);
	}

	/**
	 * @return  void
	 *
	 * @since   Kunena 6.0
	 */
	protected function setToolBarMove(): void
	{
		// Set the title bar text
		ToolbarHelper::title(Text::_('COM_KUNENA'), 'users');
		ToolbarHelper::spacer();
		ToolbarHelper::custom('moveMessages', 'save.png', 'save_f2.png', 'COM_KUNENA_MOVE_USERMESSAGES');
		ToolbarHelper::spacer();
		ToolbarHelper::cancel();
		ToolbarHelper::spacer();
	}
}
