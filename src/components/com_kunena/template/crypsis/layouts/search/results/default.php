<?php
/**
 * Kunena Component
 * @package         Kunena.Template.Crypsis
 * @subpackage      Layout.Search
 *
 * @copyright       Copyright (C) 2008 - 2022 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;

if (empty($this->results))
{
	return;
}

?>
	<h2>
		<?php echo Text::_('COM_KUNENA_SEARCH_RESULTS'); ?>
		<small>
			(<?php echo Text::sprintf('COM_KUNENA_FORUM_SEARCH', $this->escape($this->state->get('searchwords'))); ?>)
		</small>
	</h2>

<?php if ($this->error)
	:
	?>
	<div class="alert alert-error">
		<?php echo $this->error; ?>
	</div>
<?php endif; ?>

<?php
foreach ($this->results as $message)
{
	echo $this->subLayout('Search/Results/Row')->set('message', $message);
}
?>

<?php
$start = $this->pagination->limitstart + 1;
$stop  = $this->pagination->limitstart + count($this->results);
$range = $start . ' - ' . $stop;
echo Text::sprintf('COM_KUNENA_FORUM_SEARCHRESULTS', $range, $this->pagination->total);
?>
<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination);
