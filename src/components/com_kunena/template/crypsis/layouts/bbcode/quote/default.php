<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.BBCode
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
// [quote ="username post=messageid"]text content of the quote[/quote]

// Display message quoted by user.
if (!empty($this->msglink))
{
    ?>
	<blockquote style="margin: .75em 0;background: #f9fafa;border: 1px solid #e0e0e0;border-left: 2px solid #1f85bd;"><p class="kmsgtext-quote">
	<div style="padding: 12px 1px;font-size: 1.3rem;background: #fff;">
		<a href="<?php echo $this->msglink ?>"> <?php echo $this->username . " " . Text::_('COM_KUNENA_POST_WROTE'); ?>: <i class="fas fa-arrow-circle-up"></i></a>
		</div>
		<?php echo $this->content; ?>
		</blockquote>
<?php } else { ?>

<blockquote><p class="kmsgtext-quote"><?php echo $this->username . $this->content ?></p></blockquote>

<?php }  ?>
