// Runs assets/js/draft.js against in-memory storages. Reads a JSON list of steps from stdin, prints the record as JSON.
// A step is {as: <user id>, do: save|take|clear, text?, advance?: <ms>, area?: reset-all}
const fs = require('fs');
const path = process.argv[2];

class Area {
	constructor() { this.m = new Map(); }
	get length() { return this.m.size; }
	key(i) { return [...this.m.keys()][i] ?? null; }
	getItem(k) { return this.m.has(k) ? this.m.get(k) : null; }
	setItem(k, v) { this.m.set(k, String(v)); }
	removeItem(k) { this.m.delete(k); }
}

const local = new Area();
const session = new Area();
let owner = 0;
let now = 1000000;
Date.now = () => now;

global.window = {
	localStorage: local,
	sessionStorage: session,
	Joomla: { getOptions: (n) => (n === 'com_kunena.draftowner' ? owner : undefined) },
};
global.localStorage = local;

const steps = JSON.parse(fs.readFileSync(0, 'utf8'));
eval(fs.readFileSync(path, 'utf8') + '\n;global.__draft = window.KunenaDraft;');
const draft = global.__draft;

const out = { results: [] };

for (const s of steps) {
	if (s.legacy !== undefined) { local.setItem('copyKunenaeditor', s.legacy); out.results.push(null); continue; }
	if (s.advance) now += s.advance;
	owner = s.as ?? 0;
	if (s.do === 'save') { draft.save(s.text); out.results.push(null); }
	else if (s.do === 'take') out.results.push(draft.take());
	else if (s.do === 'clear') { draft.clear(); out.results.push(null); }
}

out.localKeys = [...local.m.keys()];
out.sessionKeys = [...session.m.keys()];
console.log(JSON.stringify(out));
