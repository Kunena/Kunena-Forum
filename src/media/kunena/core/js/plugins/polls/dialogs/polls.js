/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - 2020 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

CKEDITOR.dialog.add( 'pollsDialog', function( editor ) {
	return {
		title: 'Polls Properties',
		minWidth: 400,
		minHeight: 200,
		contents: 
		[
			{
				id: 'tab-basic',
				label: 'Basic Settings',
				elements: 
				[
					{
						type: 'text',
						id: 'polltitle',
						label: 'Poll title',
						default: ''
					},
					{
						type: 'button',
						id: 'polladdoption',
						label: 'Add option',
						title: 'Add option',
						onClick: function() {
							// this = CKEDITOR.ui.dialog.button
							//alert( 'Clicked: ' + this.id );
						}
					},
					{
						type: 'button',
						id: 'pollremoveoption',
						label: 'Remove option',
						title: 'Remove option',
						onClick: function() {
							// this = CKEDITOR.ui.dialog.button
							//alert( 'Clicked: ' + this.id );
						}
					},
					{
						type: 'text',
						id: 'polllifespan',
						label: 'Poll life span (optional)',
						default: ''
					}
				]
			}
		],
		onOk: function() {

		}
	};
});