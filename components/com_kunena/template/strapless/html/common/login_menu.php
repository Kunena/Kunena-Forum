<?php
/**
 * Kunena Component
 * @package Kunena.Template.Strapless
 * @subpackage Common
 *
 * @copyright (C) 2008 - 2013 Kunena Team. All rights reserved.
 * @copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.org
 **/
defined ( '_JEXEC' ) or die ();
JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');
// Basic logic has been taken from Joomla! 2.5 (mod_menu)
// HTML output emulates default Joomla! 1.5 (mod_mainmenu), but only first level is supported

// Note. It is important to remove spaces between elements.
?>

<!-- user dropdown -->

<ul class="nav pull-right">
  <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-large icon-user"></i> <b class="caret"></b></a>
    <ul class="dropdown-menu dropdown-user-account">
      <li>
        <form action="<?php echo JRoute::_('index.php?option=com_kunena'); ?>" method="post" id="login-form" class="form-inline">
          <div class="well well-small" style="margin:-5px 0;">
            <div class="userdata">
              <div id="form-login-username" class="control-group">
                <div class="controls">
                  <div class="input-prepend input-append"> <span class="add-on"><i class="icon-user tip" title="<?php echo JText::_('JGLOBAL_USERNAME') ?>"></i>
                    <label for="modlgn-username" class="element-invisible"><?php echo JText::_('JGLOBAL_USERNAME'); ?></label>
                    </span>
                    <input id="modlgn-username" type="text" name="username" class="input-small" tabindex="1" size="18" placeholder="<?php echo JText::_('JGLOBAL_USERNAME') ?>" />
                  </div>
                </div>
              </div>
              <div id="form-login-password" class="control-group">
                <div class="controls">
                  <div class="input-prepend input-append"> <span class="add-on"><i class="icon-lock tip" title="<?php echo JText::_('JGLOBAL_PASSWORD') ?>"></i>
                    <label for="modlgn-passwd" class="element-invisible"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label>
                    </span>
                    <input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="2" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" />
                  </div>
                </div>
              </div>
              <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
              <div id="form-login-remember" class="control-group checkbox">
                <label for="modlgn-remember" class="control-label"><?php echo JText::_('JGLOBAL_REMEMBER_ME') ?></label>
                <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
              </div>
              <?php endif; ?>
              <div id="form-login-submit" class="control-group">
                <div class="center">
                  <button type="submit" tabindex="3" name="Submit" class="btn btn-primary btn"><?php echo JText::_('JLOGIN') ?></button>
                </div>
              </div>
              <?php
			$usersConfig = JComponentHelper::getParams('com_users');
			if ($usersConfig->get('allowUserRegistration')) : ?>
              <ul class="unstyled center">
                <li> <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>"> <?php echo JText::_('REGISTER'); ?> <i class="icon-arrow-right"></i></a> </li>
              </ul>
              <?php endif; ?>
              <input type="hidden" name="view" value="user" />
              <input type="hidden" name="task" value="login" />
              <?php echo JHtml::_('form.token'); ?> </div>
          </div>
        </form>
      </li>
    </ul>
  </li>
</ul>
<!-- ./ user dropdown -->