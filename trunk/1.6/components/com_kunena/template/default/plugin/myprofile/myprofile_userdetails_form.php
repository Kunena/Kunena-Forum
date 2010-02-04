<?php
/**
 * @version $Id$
 * Kunena Component
 * @package Kunena
 *
 * @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.kunena.com
 *
 * Based on FireBoard Component
 * @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.bestofjoomla.com
 *
 * Based on Joomlaboard Component
 * @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @author TSMF & Jan de Graaff
 **/
defined( '_JEXEC' ) or die();


$kunena_app = & JFactory::getApplication ();
$kunena_my = &JFactory::getUser ();
$kunena_config = & CKunenaConfig::getInstance ();

require_once (KUNENA_ROOT_PATH . DS . 'includes/HTML_toolbar.php');

// used for spoof hardening
$validate = JUtility::getToken ();
?>
<script language="javascript" type="text/javascript">
// <![CDATA[
    function submitbuttons(pressbutton)
    {
        var form = document.mosUserForm;
        var r = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&|\+|\-]", "i");

        // do field validation
        if (form.name.value == "")
        {
            alert("<?php echo addslashes ( JText::_(COM_KUNENA_JS_WARN_NAME_MISSING) );?>");
        }
        else if (form.username.value == "")
        {
            alert("<?php echo addslashes ( JText::_(COM_KUNENA_JS_WARN_UNAME_MISSING) );?>");
        }
        else if (r.exec(form.username.value) || form.username.value.length < 3)
        {
            alert("<?php printf ( addslashes ( JText::_(COM_KUNENA_JS_WARN_VALID_AZ09) ), addslashes ( JText::_(COM_KUNENA_JS_PROMPT_UNAME) ), 4 );?>");
        }
        else if (form.email.value == "")
        {
            alert("<?php echo addslashes ( JText::_(COM_KUNENA_JS_WARN_MAIL_MISSING) );?>");
        }
        else if ((form.password.value != "") && (form.password.value != form.password2.value))
        {
            alert("<?php echo addslashes ( JText::_(COM_KUNENA_JS_WARN_PASSWORD2) );?>");
        }
        else if (r.exec(form.password.value))
        {
            alert("<?php printf ( addslashes ( JText::_(COM_KUNENA_JS_WARN_VALID_AZ09) ), addslashes ( JText::_(COM_KUNENA_JS_PROMPT_PASS) ), 4 );?>");
        }
        else
        {
            form.submit();
        }
    }
// ]]>
</script>
<div class="k_bt_cvr1">
<div class="k_bt_cvr2">
<div class="k_bt_cvr3">
<div class="k_bt_cvr4">
<div class="k_bt_cvr5">
<form action="index.php" method="post" name="mosUserForm">
<div style="float: right; display: none"><?php
mosToolBar::startTable ();
mosToolBar::spacer ();
mosToolBar::save ();
mosToolBar::cancel ();
mosToolBar::endtable ();
?>
</div>

<table class="kblocktable" id="kforumprofile_sub" border="0"
	cellspacing="0" cellpadding="0" width="100%">
	<thead>
		<tr>
			<th colspan="2">
			<div class="ktitle_cover"><span class="ktitle"><?php
			echo JText::_(COM_KUNENA_EDIT_TITLE);
			?></span>
			</div>
			</th>
		</tr>
	</thead>
	<tbody class="kmyprofile_general">
		<tr class="ksth">
			<th colspan="2">
			<center><?php
			echo JText::_(COM_KUNENA_MYPROFILE_PERSONAL_INFO);
			?></center>
			</th>
		</tr>
		<tr>
			<td><?php
			echo JText::_(COM_KUNENA_UNAME);
			?></td>
			<?php
			if ($kunena_config->usernamechange) {
				?>
			<td><input class="inputbox" type="text" name="username"
				value="<?php
				echo kunena_htmlspecialchars ( $row->username );
				?>"
				size="40" /></td>
			<?php
			} else {
				?>
			<td><input class="inputbox" type="hidden" name="username"
				value="<?php
				echo kunena_htmlspecialchars ( $row->username );
				?>"
				size="40" /><?php
				echo kunena_htmlspecialchars ( $row->username );
				?></td>
			<?php
			}
			?>
		</tr>
		<tr>
			<td><?php
			echo JText::_(COM_KUNENA_YOUR_NAME);
			?></td>
			<td><input class="inputbox" type="text" name="name"
				value="<?php
				echo kunena_htmlspecialchars ( $row->name );
				?>" size="40" /></td>
		</tr>
		<tr>
			<td><?php
			echo JText::_(COM_KUNENA_EMAIL);
			?></td>
			<td><input class="inputbox" type="text" name="email"
				value="<?php
				echo kunena_htmlspecialchars ( $row->email );
				?>" size="40" /></td>
		</tr>
		<tr>
			<td><?php
			echo JText::_(COM_KUNENA_PASS);
			?></td>
			<td><input class="inputbox" type="password" name="password" value=""
				size="40" /></td>
		</tr>
		<tr>
			<td><?php
			echo JText::_(COM_KUNENA_VPASS);
			?></td>
			<td><input class="inputbox" type="password" name="password2"
				size="40" /></td>
		</tr>
		<?php
		if (in_array ( $kunena_app->getCfg ( "frontend_userparams" ), array ('1', null ) )) {
			$params =& $kunena_my->getParameters(true);
			?>
		<tr>
			<td colspan="2"><?php
			echo $params->render ( 'params' );
			?></td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td colspan="2" align="center"><input type="button" name="save"
				value="<?php
				echo JText::_(COM_KUNENA_SAVE);
				?>" onclick="submitbuttons('');" /> <input
				type="reset" value="<?php
				echo JText::_(COM_KUNENA_RESET);
				?>" /></td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="id" value="<?php
echo $row->id;
?>" /> <input
	type="hidden" name="option" value="com_kunena" /> <input type="hidden"
	name="func" value="myprofile" /> <input type="hidden" name="do"
	value="usersave" /> <input type="hidden" name="task" value="" /> <input
	type="hidden" name="<?php
	echo $validate;
	?>" value="1" /></form>
</div>
</div>
</div>
</div>
</div>
