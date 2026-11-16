/**
 * The icons of the buttons of Kunena in the toolbar of CKEditor 5: 20 × 20, drawn with lines in the colour of the text, like the icons
 * of CKEditor. The label of a button stays its tooltip and its name for a screen reader.
 */
// Every shape gets its own fill="none": CKEditor fills each element of an icon without a fill attribute with the colour of the text
// (.ck-icon_inherit-color * :not([fill])), which would turn the lines into filled shapes.
const icon = (paths) => `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">${paths.replace(/<(path|circle|rect)\b(?![^>]*\bfill=)/g, '<$1 fill="none"')}</svg>`;

export const ICONS = {
	// An eye with a line through it: the text is hidden until it is clicked.
	spoiler: icon('<path d="M2 10s3-5.5 8-5.5S18 10 18 10s-3 5.5-8 5.5S2 10 2 10z"/><circle cx="10" cy="10" r="2.5"/><path d="M3.5 3.5l13 13"/>'),
	// A padlock: the text is hidden from guests.
	hidetext: icon('<rect x="4" y="9" width="12" height="8.5" rx="1.5"/><path d="M6.5 9V6.5a3.5 3.5 0 0 1 7 0V9"/>'),
	// A shield: the text is for the moderators and the author only.
	confidential: icon('<path d="M10 2.5l6 2.5v4.5c0 4-2.6 6.6-6 8-3.4-1.4-6-4-6-8V5z"/><path d="M7.5 10l2 2 3.5-3.5"/>'),
	// A shopping bag: an item of eBay.
	ebay: icon('<path d="M4.5 7h11l-1 10.5h-9z"/><path d="M7.5 7V5.5a2.5 2.5 0 0 1 5 0V7"/>'),
	// A camera: a post of Instagram.
	instagram: icon('<rect x="3" y="3" width="14" height="14" rx="4"/><circle cx="10" cy="10" r="3.2"/><circle cx="14" cy="6" r="0.6" fill="currentColor"/>'),
	// A speech bubble: a post of X.
	x: icon('<path d="M3.5 4.5h13v9h-7l-4 3v-3h-2z"/><path d="M8 7.5l4 3M12 7.5l-4 3"/>'),
	// A cloud with a sound wave: a track of SoundCloud.
	soundcloud: icon('<path d="M5.5 15.5a3.5 3.5 0 0 1-.4-7A5 5 0 0 1 14.6 8a3.8 3.8 0 0 1 .4 7.5z"/><path d="M8 11v2M10 10v3M12 11v2"/>'),
	// A pin: a map of a place.
	map: icon('<path d="M10 17.5s5.5-5 5.5-9.5a5.5 5.5 0 0 0-11 0c0 4.5 5.5 9.5 5.5 9.5z"/><circle cx="10" cy="8" r="2"/>'),
	// A screen with a play triangle: a video.
	video: icon('<rect x="2.5" y="4" width="15" height="12" rx="2"/><path d="M8.5 7.5v5l4-2.5z"/>'),
	// A smiling face: the emoticons.
	emoticons: icon('<circle cx="10" cy="10" r="7.5"/><path d="M7 11.5a3.5 3.5 0 0 0 6 0"/><path d="M7.5 7.5v.5M12.5 7.5v.5"/>'),
	// Bars of a chart: a poll.
	polls: icon('<path d="M3 17h14"/><path d="M5.5 14V9M10 14V4.5M14.5 14v-7"/>'),
};
