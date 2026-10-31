// Loads assets/js/search.js with a stand-in for jQuery (the code of the page is never run) and reports what the helpers do.
// stdin: {"base": "/7.1" | null, "terms": [...], "items": [...]}
const fs = require('fs');

const input = JSON.parse(fs.readFileSync(0, 'utf8'));
const path = process.argv[2];

let ready = null;
global.window = global;
global.jQuery = function () {
	return { ready: function (fn) { ready = fn; } };
};
global.jQuery.fn = {};
global.document = { getElementById: () => null };
global.Joomla = {
	getOptions: (name) => (name === 'system.paths' && input.base !== null ? { base: input.base, root: input.base } : undefined),
};

eval(fs.readFileSync(path, 'utf8'));

const h = global.KunenaUserSearch;
const out = {
	hasHelpers: !!h,
	urls: input.terms.map((t) => h.url(t)),
	lines: input.items.map((i) => h.menuItem(i)),
	escaped: input.terms.map((t) => h.escape(t)),
	readyRegistered: typeof ready === 'function',
};

// The ready function must not break when there is no user field and no Tribute (the list of the users).
let noTribute = 'not run';
try {
	global.XMLHttpRequest = function () {};
	global.jQuery = function () { return { is: () => false, collapse: () => {} }; };
	global.jQuery.fn = {};
	ready(global.jQuery);
	noTribute = 'ok';
} catch (e) {
	noTribute = 'threw: ' + e.message;
}
out.noTribute = noTribute;

console.log(JSON.stringify(out));
