/**
 * Kunena Component
 * @package     Kunena.Media
 *
 * @copyright   Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license     https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link        https://www.kunena.org
 **/

/**
 * The tweets of a page which the forum had not read from X yet: the page shows a link to each one, and the tweet is read after the
 * page (task misc.tweet) and put in the place of the link. When it cannot be read, the link stays.
 */
document.addEventListener('DOMContentLoaded', () => {
	document.querySelectorAll('.kunena-tweet-load[data-tweet-url]').forEach((box) => {
		fetch(box.dataset.tweetUrl, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
			.then((response) => (response.ok ? response.json() : null))
			.then((data) => {
				if (data && typeof data.html === 'string' && data.html.trim() !== '') {
					box.innerHTML = data.html;
					box.classList.remove('kunena-tweet-load');
				}
			})
			.catch(() => {});
	});
});
