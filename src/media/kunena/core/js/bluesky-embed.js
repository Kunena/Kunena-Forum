/**
 * Kunena Component
 * @package Kunena.Media
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

// The frame of a Bluesky post (embed.bsky.app) sends its height with the id which it got in its address: the frame of that id
// gets the height, so that the post is shown whole, without a scroll bar of its own.
window.addEventListener('message', (event) => {
	if (event.origin !== 'https://embed.bsky.app' || !event.data || typeof event.data !== 'object') {
		return;
	}

	const id = String(event.data.id || '');
	const height = Number(event.data.height);

	if (!/^[0-9a-f]{12}$/.test(id) || !Number.isFinite(height) || height <= 0) {
		return;
	}

	const frame = document.querySelector('iframe.kunena-bluesky[data-bluesky-id="' + id + '"]');

	if (frame) {
		frame.style.height = Math.min(Math.ceil(height), 5000) + 'px';
	}
});
