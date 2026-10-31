/**
 * Runs one of the scripts of Kunena against a recording stand-in for jQuery, with hostile data from the server, and
 * prints what the script did with it as JSON: which strings were handed to jQuery as HTML, and which values were set as
 * text, value or attribute (those are safe). No browser and no jQuery are needed.
 *
 *   node dom-sinks.js <scenario> <path-to-script | --inline>   (--inline reads the script from stdin)
 *
 * scenario: kmod | gallery | upload
 */
'use strict';

const fs = require('fs');
const vm = require('vm');

const [scenario, target] = process.argv.slice(2);
const code = target === '--inline' ? fs.readFileSync(0, 'utf8') : fs.readFileSync(target, 'utf8');

const SUBJECT   = '</option><img src=x onerror=alert(1)>';
const FILENAME  = '"><img src=x onerror=alert(2)>';
const URL_VALUE = 'x" onerror="alert(3)';
const NAME_ATTR = '" autofocus onfocus="alert(4)';

const record = { htmlStrings: [], texts: [], vals: [], attrs: [] };
const handlers = {};            // event name -> function (last registered wins)
const selectorHandlers = {};    // "selector|event" -> function

const isHtmlString = (v) => typeof v === 'string' && v.indexOf('<') !== -1;

function makeNode(label) {
    const node = new Proxy(function () {}, {
        get(_t, prop) {
            if (prop === Symbol.toPrimitive) return () => label;
            if (prop === 'length') return 1;
            if (prop === 'then') return undefined;
            return (...args) => {
                switch (prop) {
                    case 'val':
                        if (args.length) { record.vals.push(String(args[0])); return node; }
                        return '';
                    case 'text':
                        if (args.length) { record.texts.push(String(args[0])); return node; }
                        return '';
                    case 'attr':
                    case 'prop':
                        if (args.length === 1 && args[0] && typeof args[0] === 'object') {
                            Object.entries(args[0]).forEach(([k, v]) => record.attrs.push(k + '=' + v));
                        } else if (args.length === 2) {
                            record.attrs.push(args[0] + '=' + args[1]);
                        } else {
                            return '';
                        }
                        return node;
                    case 'append':
                    case 'prepend':
                    case 'html':
                    case 'after':
                    case 'before':
                    case 'replaceWith':
                    case 'wrap':
                        args.forEach((a) => { if (isHtmlString(a)) record.htmlStrings.push(a); });
                        return node;
                    case 'change':
                    case 'click':
                    case 'on':
                    case 'bind': {
                        const fn = args.find((a) => typeof a === 'function');
                        const evt = typeof args[0] === 'string' ? args[0] : prop;
                        if (fn) {
                            handlers[evt] = fn;
                            selectorHandlers[label + '|' + evt] = fn;
                        }
                        return node;
                    }
                    case 'hasClass':
                    case 'is':
                        return false;
                    case 'data':
                        return args.length ? node : {};
                    default:
                        return node;
                }
            };
        },
    });

    return node;
}

const nodes = {};
const byLabel = (label) => (nodes[label] = nodes[label] || makeNode(label));

function $(arg) {
    if (typeof arg === 'function') { arg($); return makeNode('ready'); }
    if (arg !== undefined && arg === context.document) return byLabel('document');
    if (arg && (typeof arg === 'object' || typeof arg === 'function') && arg !== globalThis) return arg;
    if (isHtmlString(arg)) {
        // $('<option>') and $('<input type="radio">') build one element: fine. Anything with data in it is not.
        if (!/^<[a-z]+(\s[^<>]*)?\/?>$/i.test(arg.trim())) record.htmlStrings.push(arg);
        return makeNode(arg);
    }
    return byLabel(String(arg));
}

$.each = (obj, fn) => {
    if (Array.isArray(obj)) obj.forEach((v, i) => fn.call(v, i, v));
    else if (obj && typeof obj === 'object') Object.keys(obj).forEach((k) => fn.call(obj[k], k, obj[k]));
    return obj;
};
$.fn = {};
$.support = { fileInput: true };
$.extend = Object.assign;

const fixtures = {
    kmod:    { topiclist: [{ id: 5, subject: SUBJECT }] },
    gallery: [{ filename: FILENAME, url: URL_VALUE }],
};

const promise = (fixture) => {
    const p = { done(cb) { if (fixture !== undefined) cb(fixture); return p; }, fail() { return p; }, always() { return p; } };

    return p;
};

$.getJSON = () => promise(fixtures.kmod);
$.ajax = () => promise(fixtures.gallery);

const jQuery = (arg) => {
    if (arg === context.document) return { ready(fn) { fn($); return this; } };
    if (typeof arg === 'function') { arg($); return makeNode('ready'); }

    return $(arg);
};
Object.assign(jQuery, $);

const context = vm.createContext({
    console,
    jQuery,
    $,
    Joomla: { Text: { _: (k) => k }, getOptions: () => '', sanitizeHtml: (s) => s },
    bootstrap: { Popover: function () {}, Tooltip: function () {}, Modal: function () {} },
    kunena_url_ajax: '/ajax',
    setTimeout: () => 0,
    navigator: { userAgent: 'node-harness', platform: '' },
    URL: { createObjectURL: () => '', revokeObjectURL: () => {} },
    window: {},
});
context.document = { querySelectorAll: () => [], addEventListener: () => {}, getElementById: () => null, body: {} };
context.window.document  = context.document;
context.window.navigator = context.navigator;
context.window.URL       = context.URL;

try {
    vm.runInContext(code, context, { filename: 'script-under-test.js' });
} catch (e) {
    record.error = 'load: ' + e.message;
}

try {
    if (scenario === 'kmod') {
        const fn = handlers.change && (selectorHandlers['#kmod_categories|change'] || handlers.change);
        if (!selectorHandlers['#kmod_categories|change']) throw new Error('no change handler on #kmod_categories');
        selectorHandlers['#kmod_categories|change'].call(byLabel('#kmod_categories'));
    } else if (scenario === 'gallery') {
        if (!selectorHandlers['#avatar_gallery_select|change']) throw new Error('no change handler on #avatar_gallery_select');
        selectorHandlers['#avatar_gallery_select|change'].call(byLabel('#avatar_gallery_select'));
    } else if (scenario === 'upload') {
        if (!handlers.fileuploaddone) throw new Error('no fileuploaddone handler');
        const context1 = makeNode('context');
        handlers.fileuploaddone.call(byLabel('#fileupload'), {}, {
            context: context1,
            files: [],
            result: { success: true, location: 'x', data: { id: 7, filename: NAME_ATTR } },
        });
    }
} catch (e) {
    record.error = (record.error ? record.error + '; ' : '') + 'run: ' + e.message;
}

record.sentinels = { SUBJECT, FILENAME, URL_VALUE, NAME_ATTR };
process.stdout.write(JSON.stringify(record));
