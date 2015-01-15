<?php
/**
 * Kunena Component
 * @package     Kunena.Template.Crypsis
 * @subpackage  Layout.Search
 *
 * @copyright   (C) 2008 - 2015 Kunena Team. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        http://www.kunena.org
 **/
defined('_JEXEC') or die;

if (empty($this->results)) {
	return;
}
$config = KunenaFactory::getConfig();
// Load FancyBox library if enabled in configuration
if ($config->lightbox == 1)
{
	$template = KunenaTemplate::getInstance();
	if ( $template->params->get('lightboxColor') == 'white') {
		$this->addStyleSheet('css/fancybox-white.css');
	}
	else  {
		$this->addStyleSheet('css/fancybox-black.css');
	}
	$this->addScript('js/fancybox.js');
	JFactory::getDocument()->addScriptDeclaration('
				jQuery(document).ready(function() {
					jQuery(".fancybox-button").fancybox({
						prevEffect		: \'none\',
						nextEffect		: \'none\',
						closeBtn		:  true,
						helpers		: {
							title	: { type : \'inside\' },
							buttons	: {}
						}
					});
				});
			');
}
?>
<h2>
	<?php echo JText::_('COM_KUNENA_SEARCH_RESULTS'); ?>
	<small>
		(<?php echo JText::sprintf ('COM_KUNENA_FORUM_SEARCH', $this->escape($this->state->get('searchwords'))); ?>)
	</small>
</h2>

<?php if ($this->error) : ?>
<div class="alert alert-error">
	<?php echo $this->error; ?>
</div>
<?php endif; ?>

<?php
foreach ($this->results as $message) {
	// TODO: use the default message layout...
	echo $this->subLayout('Search/Results/Row')->set('message', $message);
}
?>

<?php
$start = $this->pagination->limitstart + 1;
$stop = $this->pagination->limitstart + count($this->results);
$range = $start . ' - ' . $stop;
echo JText::sprintf('COM_KUNENA_FORUM_SEARCHRESULTS', $range, $this->pagination->total);
?>
<?php echo $this->subLayout('Widget/Pagination/List')->set('pagination', $this->pagination); ?>
