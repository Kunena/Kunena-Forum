(function () {
	'use strict';

	/**
	 * Check if the passed argument is the
	 * the passed type.
	 *
	 * @param {string} type
	 * @param {*} arg
	 * @returns {boolean}
	 */
	function isTypeof(type, arg) {
		return typeof arg === type;
	}

	/**
	 * @type {function(*): boolean}
	 */
	var isString = isTypeof.bind(null, 'string');

	/**
	 * @type {function(*): boolean}
	 */
	var isUndefined = isTypeof.bind(null, 'undefined');

	/**
	 * @type {function(*): boolean}
	 */
	var isFunction = isTypeof.bind(null, 'function');

	/**
	 * @type {function(*): boolean}
	 */
	var isNumber = isTypeof.bind(null, 'number');


	/**
	 * Returns true if an object has no keys
	 *
	 * @param {!Object} obj
	 * @returns {boolean}
	 */
	function isEmptyObject(obj) {
		return !Object.keys(obj).length;
	}

	/**
	 * Extends the first object with any extra objects passed
	 *
	 * If the first argument is boolean and set to true
	 * it will extend child arrays and objects recursively.
	 *
	 * @param {!Object|boolean} targetArg
	 * @param {...Object} source
	 * @return {Object}
	 */
	function extend(targetArg, sourceArg) {
		var isTargetBoolean = targetArg === !!targetArg;
		var i      = isTargetBoolean ? 2 : 1;
		var target = isTargetBoolean ? sourceArg : targetArg;
		var isDeep = isTargetBoolean ? targetArg : false;

		function isObject(value) {
			return value !== null && typeof value === 'object' &&
				Object.getPrototypeOf(value) === Object.prototype;
		}

		for (; i < arguments.length; i++) {
			var source = arguments[i];

			// Copy all properties for jQuery compatibility
			/* eslint guard-for-in: off */
			for (var key in source) {
				var targetValue = target[key];
				var value = source[key];

				// Skip undefined values to match jQuery
				if (isUndefined(value)) {
					continue;
				}

				// Skip special keys to prevent prototype pollution
				if (key === '__proto__' || key === 'constructor') {
					continue;
				}

				var isValueObject = isObject(value);
				var isValueArray = Array.isArray(value);

				if (isDeep && (isValueObject || isValueArray)) {
					// Can only merge if target type matches otherwise create
					// new target to merge into
					var isSameType = isObject(targetValue) === isValueObject &&
						Array.isArray(targetValue) === isValueArray;

					target[key] = extend(
						true,
						isSameType ? targetValue : (isValueArray ? [] : {}),
						value
					);
				} else {
					target[key] = value;
				}
			}
		}

		return target;
	}

	/**
	 * Removes an item from the passed array
	 *
	 * @param {!Array} arr
	 * @param {*} item
	 */
	function arrayRemove(arr, item) {
		var i = arr.indexOf(item);

		if (i > -1) {
			arr.splice(i, 1);
		}
	}

	/**
	 * Iterates over an array or object
	 *
	 * @param {!Object|Array} obj
	 * @param {function(*, *)} fn
	 */
	function each(obj, fn) {
		if (Array.isArray(obj) || 'length' in obj && isNumber(obj.length)) {
			for (var i = 0; i < obj.length; i++) {
				fn(i, obj[i]);
			}
		} else {
			Object.keys(obj).forEach(function (key) {
				fn(key, obj[key]);
			});
		}
	}

	/**
	 * Cache of camelCase CSS property names
	 * @type {Object<string, string>}
	 */
	var cssPropertyNameCache = {};

	/**
	 * Node type constant for element nodes
	 *
	 * @type {number}
	 */
	var ELEMENT_NODE = 1;

	/**
	 * Node type constant for text nodes
	 *
	 * @type {number}
	 */
	var TEXT_NODE = 3;

	/**
	 * Node type constant for comment nodes
	 *
	 * @type {number}
	 */
	var COMMENT_NODE = 8;

	function toFloat(value) {
		value = parseFloat(value);

		return isFinite(value) ? value : 0;
	}

	/**
	 * Creates an element with the specified attributes
	 *
	 * Will create it in the current document unless context
	 * is specified.
	 *
	 * @param {!string} tag
	 * @param {!Object<string, string>} [attributes]
	 * @param {!Document} [context]
	 * @returns {!HTMLElement}
	 */
	function createElement(tag, attributes, context) {
		var node = (context || document).createElement(tag);

		each(attributes || {}, function (key, value) {
			if (key === 'style') {
				node.style.cssText = value;
			} else if (key in node) {
				node[key] = value;
			} else {
				node.setAttribute(key, value);
			}
		});

		return node;
	}

	/**
	 * Gets the first parent node that matches the selector
	 *
	 * @param {!HTMLElement} node
	 * @param {!string} [selector]
	 * @returns {HTMLElement|undefined}
	 */
	function parent(node, selector) {
		var parent = node || {};

		while ((parent = parent.parentNode) && !/(9|11)/.test(parent.nodeType)) {
			if (!selector || is(parent, selector)) {
				return parent;
			}
		}
	}

	/**
	 * Checks the passed node and all parents and
	 * returns the first matching node if any.
	 *
	 * @param {!HTMLElement} node
	 * @param {!string} selector
	 * @returns {HTMLElement|undefined}
	 */
	function closest(node, selector) {
		return is(node, selector) ? node : parent(node, selector);
	}

	/**
	 * Removes the node from the DOM
	 *
	 * @param {!HTMLElement} node
	 */
	function remove(node) {
		if (node.parentNode) {
			node.parentNode.removeChild(node);
		}
	}

	/**
	 * Appends child to parent node
	 *
	 * @param {!HTMLElement} node
	 * @param {!HTMLElement} child
	 */
	function appendChild(node, child) {
		node.appendChild(child);
	}

	/**
	 * Finds any child nodes that match the selector
	 *
	 * @param {!HTMLElement} node
	 * @param {!string} selector
	 * @returns {NodeList}
	 */
	function find(node, selector) {
		return node.querySelectorAll(selector);
	}

	/**
	 * For on() and off() if to add/remove the event
	 * to the capture phase
	 *
	 * @type {boolean}
	 */
	var EVENT_CAPTURE = true;

	/**
	 * Adds an event listener for the specified events.
	 *
	 * Events should be a space separated list of events.
	 *
	 * If selector is specified the handler will only be
	 * called when the event target matches the selector.
	 *
	 * @param {!Node} node
	 * @param {string} events
	 * @param {string} [selector]
	 * @param {function(Object)} fn
	 * @param {boolean} [capture=false]
	 * @see off()
	 */
	// eslint-disable-next-line max-params
	function on(node, events, selector, fn, capture) {
		events.split(' ').forEach(function (event) {
			var handler;

			if (isString(selector)) {
				handler = fn['_sce-event-' + event + selector] || function (e) {
					var target = e.target;
					while (target && target !== node) {
						if (is(target, selector)) {
							fn.call(target, e);
							return;
						}

						target = target.parentNode;
					}
				};

				fn['_sce-event-' + event + selector] = handler;
			} else {
				handler = selector;
				capture = fn;
			}

			node.addEventListener(event, handler, capture || false);
		});
	}

	/**
	 * Removes an event listener for the specified events.
	 *
	 * @param {!Node} node
	 * @param {string} events
	 * @param {string} [selector]
	 * @param {function(Object)} fn
	 * @param {boolean} [capture=false]
	 * @see on()
	 */
	// eslint-disable-next-line max-params
	function off(node, events, selector, fn, capture) {
		events.split(' ').forEach(function (event) {
			var handler;

			if (isString(selector)) {
				handler = fn['_sce-event-' + event + selector];
			} else {
				handler = selector;
				capture = fn;
			}

			node.removeEventListener(event, handler, capture || false);
		});
	}

	/**
	 * If only attr param is specified it will get
	 * the value of the attr param.
	 *
	 * If value is specified but null the attribute
	 * will be removed otherwise the attr value will
	 * be set to the passed value.
	 *
	 * @param {!HTMLElement} node
	 * @param {!string} attr
	 * @param {?string} [value]
	 */
	function attr(node, attr, value) {
		if (arguments.length < 3) {
			return node.getAttribute(attr);
		}

		// eslint-disable-next-line eqeqeq, no-eq-null
		if (value == null) {
			removeAttr(node, attr);
		} else {
			node.setAttribute(attr, value);
		}
	}

	/**
	 * Removes the specified attribute
	 *
	 * @param {!HTMLElement} node
	 * @param {!string} attr
	 */
	function removeAttr(node, attr) {
		node.removeAttribute(attr);
	}

	/**
	 * Sets the passed elements display to none
	 *
	 * @param {!HTMLElement} node
	 */
	function hide(node) {
		css(node, 'display', 'none');
	}

	/**
	 * Sets the passed elements display to default
	 *
	 * @param {!HTMLElement} node
	 */
	function show(node) {
		css(node, 'display', '');
	}

	/**
	 * Toggles an elements visibility
	 *
	 * @param {!HTMLElement} node
	 */
	function toggle(node) {
		if (isVisible(node)) {
			hide(node);
		} else {
			show(node);
		}
	}

	/**
	 * Gets a computed CSS values or sets an inline CSS value
	 *
	 * Rules should be in camelCase format and not
	 * hyphenated like CSS properties.
	 *
	 * @param {!HTMLElement} node
	 * @param {!Object|string} rule
	 * @param {string|number} [value]
	 * @return {string|number|undefined}
	 */
	function css(node, rule, value) {
		if (arguments.length < 3) {
			if (isString(rule)) {
				return node.nodeType === 1 ? getComputedStyle(node)[rule] : null;
			}

			each(rule, function (key, value) {
				css(node, key, value);
			});
		} else {
			// isNaN returns false for null, false and empty strings
			// so need to check it's truthy or 0
			var isNumeric = (value || value === 0) && !isNaN(value);
			node.style[rule] = isNumeric ? value + 'px' : value;
		}
	}


	/**
	 * Gets or sets the data attributes on a node
	 *
	 * Unlike the jQuery version this only stores data
	 * in the DOM attributes which means only strings
	 * can be stored.
	 *
	 * @param {Node} node
	 * @param {string} [key]
	 * @param {string} [value]
	 * @return {Object|undefined}
	 */
	function data(node, key, value) {
		var argsLength = arguments.length;
		var data = {};

		if (node.nodeType === ELEMENT_NODE) {
			if (argsLength === 1) {
				each(node.attributes, function (_, attr) {
					if (/^data\-/i.test(attr.name)) {
						data[attr.name.substr(5)] = attr.value;
					}
				});

				return data;
			}

			if (argsLength === 2) {
				return attr(node, 'data-' + key);
			}

			attr(node, 'data-' + key, String(value));
		}
	}

	/**
	 * Checks if node matches the given selector.
	 *
	 * @param {?HTMLElement} node
	 * @param {string} selector
	 * @returns {boolean}
	 */
	function is(node, selector) {
		var result = false;

		if (node && node.nodeType === ELEMENT_NODE) {
			result = (node.matches || node.msMatchesSelector ||
				node.webkitMatchesSelector).call(node, selector);
		}

		return result;
	}


	/**
	 * Returns true if node contains child otherwise false.
	 *
	 * This differs from the DOM contains() method in that
	 * if node and child are equal this will return false.
	 *
	 * @param {!Node} node
	 * @param {HTMLElement} child
	 * @returns {boolean}
	 */
	function contains(node, child) {
		return node !== child && node.contains && node.contains(child);
	}

	/**
	 * @param {Node} node
	 * @param {string} [selector]
	 * @returns {?HTMLElement}
	 */
	function previousElementSibling(node, selector) {
		var prev = node.previousElementSibling;

		if (selector && prev) {
			return is(prev, selector) ? prev : null;
		}

		return prev;
	}

	/**
	 * @param {!Node} node
	 * @param {!Node} refNode
	 * @returns {Node}
	 */
	function insertBefore(node, refNode) {
		return refNode.parentNode.insertBefore(node, refNode);
	}

	/**
	 * @param {?HTMLElement} node
	 * @returns {!Array.<string>}
	 */
	function classes(node) {
		return node.className.trim().split(/\s+/);
	}

	/**
	 * @param {?HTMLElement} node
	 * @param {string} className
	 * @returns {boolean}
	 */
	function hasClass(node, className) {
		return is(node, '.' + className);
	}

	/**
	 * @param {!HTMLElement} node
	 * @param {string} className
	 */
	function addClass(node, className) {
		var classList = classes(node);

		if (classList.indexOf(className) < 0) {
			classList.push(className);
		}

		node.className = classList.join(' ');
	}

	/**
	 * @param {!HTMLElement} node
	 * @param {string} className
	 */
	function removeClass(node, className) {
		var classList = classes(node);

		arrayRemove(classList, className);

		node.className = classList.join(' ');
	}

	/**
	 * Toggles a class on node.
	 *
	 * If state is specified and is truthy it will add
	 * the class.
	 *
	 * If state is specified and is falsey it will remove
	 * the class.
	 *
	 * @param {HTMLElement} node
	 * @param {string} className
	 * @param {boolean} [state]
	 */
	function toggleClass(node, className, state) {
		state = isUndefined(state) ? !hasClass(node, className) : state;

		if (state) {
			addClass(node, className);
		} else {
			removeClass(node, className);
		}
	}

	/**
	 * Gets or sets the width of the passed node.
	 *
	 * @param {HTMLElement} node
	 * @param {number|string} [value]
	 * @returns {number|undefined}
	 */
	function width(node, value) {
		if (isUndefined(value)) {
			var cs = getComputedStyle(node);
			var padding = toFloat(cs.paddingLeft) + toFloat(cs.paddingRight);
			var border = toFloat(cs.borderLeftWidth) + toFloat(cs.borderRightWidth);

			return node.offsetWidth - padding - border;
		}

		css(node, 'width', value);
	}

	/**
	 * Gets or sets the height of the passed node.
	 *
	 * @param {HTMLElement} node
	 * @param {number|string} [value]
	 * @returns {number|undefined}
	 */
	function height(node, value) {
		if (isUndefined(value)) {
			var cs = getComputedStyle(node);
			var padding = toFloat(cs.paddingTop) + toFloat(cs.paddingBottom);
			var border = toFloat(cs.borderTopWidth) + toFloat(cs.borderBottomWidth);

			return node.offsetHeight - padding - border;
		}

		css(node, 'height', value);
	}

	/**
	 * Triggers a custom event with the specified name and
	 * sets the detail property to the data object passed.
	 *
	 * @param {HTMLElement} node
	 * @param {string} eventName
	 * @param {Object} [data]
	 */
	function trigger(node, eventName, data) {
		var event;

		if (isFunction(window.CustomEvent)) {
			event = new CustomEvent(eventName, {
				bubbles: true,
				cancelable: true,
				detail: data
			});
		} else {
			event = node.ownerDocument.createEvent('CustomEvent');
			event.initCustomEvent(eventName, true, true, data);
		}

		node.dispatchEvent(event);
	}

	/**
	 * Returns if a node is visible.
	 *
	 * @param {HTMLElement}
	 * @returns {boolean}
	 */
	function isVisible(node) {
		return !!node.getClientRects().length;
	}

	/**
	 * Convert CSS property names into camel case
	 *
	 * @param {string} string
	 * @returns {string}
	 */
	function camelCase(string) {
		return string
			.replace(/^-ms-/, 'ms-')
			.replace(/-(\w)/g, function (match, char) {
				return char.toUpperCase();
			});
	}


	/**
	 * Loop all child nodes of the passed node
	 *
	 * The function should accept 1 parameter being the node.
	 * If the function returns false the loop will be exited.
	 *
	 * @param  {HTMLElement} node
	 * @param  {function} func           Callback which is called with every
	 *                                   child node as the first argument.
	 * @param  {boolean} innermostFirst  If the innermost node should be passed
	 *                                   to the function before it's parents.
	 * @param  {boolean} siblingsOnly    If to only traverse the nodes siblings
	 * @param  {boolean} [reverse=false] If to traverse the nodes in reverse
	 */
	// eslint-disable-next-line max-params
	function traverse(node, func, innermostFirst, siblingsOnly, reverse) {
		node = reverse ? node.lastChild : node.firstChild;

		while (node) {
			var next = reverse ? node.previousSibling : node.nextSibling;

			if (
				(!innermostFirst && func(node) === false) ||
				(!siblingsOnly && traverse(
					node, func, innermostFirst, siblingsOnly, reverse
				) === false) ||
				(innermostFirst && func(node) === false)
			) {
				return false;
			}

			node = next;
		}
	}

	/**
	 * Like traverse but loops in reverse
	 * @see traverse
	 */
	function rTraverse(node, func, innermostFirst, siblingsOnly) {
		traverse(node, func, innermostFirst, siblingsOnly, true);
	}

	/**
	 * Parses HTML into a document fragment
	 *
	 * @param {string} html
	 * @param {Document} [context]
	 * @since 1.4.4
	 * @return {DocumentFragment}
	 */
	function parseHTML(html, context) {
		context = context || document;

		var	ret = context.createDocumentFragment();
		var tmp = createElement('div', {}, context);

		tmp.innerHTML = html;

		while (tmp.firstChild) {
			appendChild(ret, tmp.firstChild);
		}

		return ret;
	}

	/**
	 * Checks if an element has any styling.
	 *
	 * It has styling if it is not a plain <div> or <p> or
	 * if it has a class, style attribute or data.
	 *
	 * @param  {HTMLElement} elm
	 * @return {boolean}
	 * @since 1.4.4
	 */
	function hasStyling(node) {
		return node && (!is(node, 'p,div') || node.className ||
			attr(node, 'style') || !isEmptyObject(data(node)));
	}

	/**
	 * Converts an element from one type to another.
	 *
	 * For example it can convert the element <b> to <strong>
	 *
	 * @param  {HTMLElement} element
	 * @param  {string}      toTagName
	 * @return {HTMLElement}
	 * @since 1.4.4
	 */
	function convertElement(element, toTagName) {
		var newElement = createElement(toTagName, {}, element.ownerDocument);

		each(element.attributes, function (_, attribute) {
			// Some browsers parse invalid attributes names like
			// 'size"2' which throw an exception when set, just
			// ignore these.
			try {
				attr(newElement, attribute.name, attribute.value);
			} catch (ex) {}
		});

		while (element.firstChild) {
			appendChild(newElement, element.firstChild);
		}

		element.parentNode.replaceChild(newElement, element);

		return newElement;
	}

	/**
	 * List of block level elements separated by bars (|)
	 *
	 * @type {string}
	 */
	var blockLevelList = '|body|hr|p|div|h1|h2|h3|h4|h5|h6|address|pre|' +
		'form|table|tbody|thead|tfoot|th|tr|td|li|ol|ul|blockquote|center|' +
		'details|section|article|aside|nav|main|header|hgroup|footer|fieldset|' +
		'dl|dt|dd|figure|figcaption|';

	/**
	 * List of elements that do not allow children separated by bars (|)
	 *
	 * @param {Node} node
	 * @return {boolean}
	 * @since  1.4.5
	 */
	function canHaveChildren(node) {
		// 1  = Element
		// 9  = Document
		// 11 = Document Fragment
		if (!/11?|9/.test(node.nodeType)) {
			return false;
		}

		// List of empty HTML tags separated by bar (|) character.
		// Source: http://www.w3.org/TR/html4/index/elements.html
		// Source: http://www.w3.org/TR/html5/syntax.html#void-elements
		return ('|iframe|area|base|basefont|br|col|frame|hr|img|input|wbr' +
			'|isindex|link|meta|param|command|embed|keygen|source|track|' +
			'object|').indexOf('|' + node.nodeName.toLowerCase() + '|') < 0;
	}

	/**
	 * Checks if an element is inline
	 *
	 * @param {HTMLElement} elm
	 * @param {boolean} [includeCodeAsBlock=false]
	 * @return {boolean}
	 */
	function isInline(elm, includeCodeAsBlock) {
		var tagName,
			nodeType = (elm || {}).nodeType || TEXT_NODE;

		if (nodeType !== ELEMENT_NODE) {
			return nodeType === TEXT_NODE;
		}

		tagName = elm.tagName.toLowerCase();

		if (tagName === 'code') {
			return !includeCodeAsBlock;
		}

		return blockLevelList.indexOf('|' + tagName + '|') < 0;
	}

	/**
	 * Copy the CSS from 1 node to another.
	 *
	 * Only copies CSS defined on the element e.g. style attr.
	 *
	 * @param {HTMLElement} from
	 * @param {HTMLElement} to
	 * @deprecated since v3.1.0
	 */
	function copyCSS(from, to) {
		if (to.style && from.style) {
			to.style.cssText = from.style.cssText + to.style.cssText;
		}
	}

	/**
	 * Checks if a DOM node is empty
	 *
	 * @param {Node} node
	 * @returns {boolean}
	 */
	function isEmpty(node) {
		if (node.lastChild && isEmpty(node.lastChild)) {
			remove(node.lastChild);
		}

		return node.nodeType === 3 ? !node.nodeValue :
			(canHaveChildren(node) && !node.childNodes.length);
	}

	/**
	 * Fixes block level elements inside in inline elements.
	 *
	 * Also fixes invalid list nesting by placing nested lists
	 * inside the previous li tag or wrapping them in an li tag.
	 *
	 * @param {HTMLElement} node
	 */
	function fixNesting(node) {
		traverse(node, function (node) {
			var list = 'ul,ol',
				isBlock = !isInline(node, true) && node.nodeType !== COMMENT_NODE,
				parent = node.parentNode;

			// Any blocklevel element inside an inline element needs fixing.
			// Also <p> tags that contain blocks should be fixed
			if (isBlock && (isInline(parent, true) || parent.tagName === 'P')) {
				// Find the last inline parent node
				var	lastInlineParent = node;
				while (isInline(lastInlineParent.parentNode, true) ||
					lastInlineParent.parentNode.tagName === 'P') {
					lastInlineParent = lastInlineParent.parentNode;
				}

				var before = extractContents(lastInlineParent, node);
				var middle = node;

				// Clone inline styling and apply it to the blocks children
				while (parent && isInline(parent, true)) {
					if (parent.nodeType === ELEMENT_NODE) {
						var clone = parent.cloneNode();
						while (middle.firstChild) {
							appendChild(clone, middle.firstChild);
						}

						appendChild(middle, clone);
					}
					parent = parent.parentNode;
				}

				insertBefore(middle, lastInlineParent);
				if (!isEmpty(before)) {
					insertBefore(before, middle);
				}
				if (isEmpty(lastInlineParent)) {
					remove(lastInlineParent);
				}
			}

			// Fix invalid nested lists which should be wrapped in an li tag
			if (isBlock && is(node, list) && is(node.parentNode, list)) {
				var li = previousElementSibling(node, 'li');

				if (!li) {
					li = createElement('li');
					insertBefore(li, node);
				}

				appendChild(li, node);
			}
		});
	}

	/**
	 * Finds the common parent of two nodes
	 *
	 * @param {!HTMLElement} node1
	 * @param {!HTMLElement} node2
	 * @return {?HTMLElement}
	 */
	function findCommonAncestor(node1, node2) {
		while ((node1 = node1.parentNode)) {
			if (contains(node1, node2)) {
				return node1;
			}
		}
	}

	/**
	 * @param {?Node}
	 * @param {boolean} [previous=false]
	 * @returns {?Node}
	 */
	function getSibling(node, previous) {
		if (!node) {
			return null;
		}

		return (previous ? node.previousSibling : node.nextSibling) ||
			getSibling(node.parentNode, previous);
	}

	/**
	 * Removes unused whitespace from the root and all it's children.
	 *
	 * @param {!HTMLElement} root
	 * @since 1.4.3
	 */
	function removeWhiteSpace(root) {
		var	nodeValue, nodeType, next, previous, previousSibling,
			nextNode, trimStart,
			cssWhiteSpace = css(root, 'whiteSpace'),
			// Preserve newlines if is pre-line
			preserveNewLines = /line$/i.test(cssWhiteSpace),
			node = root.firstChild;

		// Skip pre & pre-wrap with any vendor prefix
		if (/pre(\-wrap)?$/i.test(cssWhiteSpace)) {
			return;
		}

		while (node) {
			nextNode  = node.nextSibling;
			nodeValue = node.nodeValue;
			nodeType  = node.nodeType;

			if (nodeType === ELEMENT_NODE && node.firstChild) {
				removeWhiteSpace(node);
			}

			if (nodeType === TEXT_NODE) {
				next      = getSibling(node);
				previous  = getSibling(node, true);
				trimStart = false;

				while (hasClass(previous, 'sceditor-ignore')) {
					previous = getSibling(previous, true);
				}

				// If previous sibling isn't inline or is a textnode that
				// ends in whitespace, time the start whitespace
				if (isInline(node) && previous) {
					previousSibling = previous;

					while (previousSibling.lastChild) {
						previousSibling = previousSibling.lastChild;

						// eslint-disable-next-line max-depth
						while (hasClass(previousSibling, 'sceditor-ignore')) {
							previousSibling = getSibling(previousSibling, true);
						}
					}

					trimStart = previousSibling.nodeType === TEXT_NODE ?
						/[\t\n\r ]$/.test(previousSibling.nodeValue) :
						!isInline(previousSibling);
				}

				// Clear zero width spaces
				nodeValue = nodeValue.replace(/\u200B/g, '');

				// Strip leading whitespace
				if (!previous || !isInline(previous) || trimStart) {
					nodeValue = nodeValue.replace(
						preserveNewLines ? /^[\t ]+/ : /^[\t\n\r ]+/,
						''
					);
				}

				// Strip trailing whitespace
				if (!next || !isInline(next)) {
					nodeValue = nodeValue.replace(
						preserveNewLines ? /[\t ]+$/ : /[\t\n\r ]+$/,
						''
					);
				}

				// Remove empty text nodes
				if (!nodeValue.length) {
					remove(node);
				} else {
					node.nodeValue = nodeValue.replace(
						preserveNewLines ? /[\t ]+/g : /[\t\n\r ]+/g,
						' '
					);
				}
			}

			node = nextNode;
		}
	}

	/**
	 * Extracts all the nodes between the start and end nodes
	 *
	 * @param {HTMLElement} startNode	The node to start extracting at
	 * @param {HTMLElement} endNode		The node to stop extracting at
	 * @return {DocumentFragment}
	 */
	function extractContents(startNode, endNode) {
		var range = startNode.ownerDocument.createRange();

		range.setStartBefore(startNode);
		range.setEndAfter(endNode);

		return range.extractContents();
	}

	/**
	 * Gets the offset position of an element
	 *
	 * @param  {HTMLElement} node
	 * @return {Object} An object with left and top properties
	 */
	function getOffset(node) {
		var	left = 0,
			top = 0;

		while (node) {
			left += node.offsetLeft;
			top  += node.offsetTop;
			node  = node.offsetParent;
		}

		return {
			left: left,
			top: top
		};
	}

	/**
	 * Gets the value of a CSS property from the elements style attribute
	 *
	 * @param  {HTMLElement} elm
	 * @param  {string} property
	 * @return {string}
	 */
	function getStyle(elm, property) {
		var	styleValue,
			elmStyle = elm.style;

		if (!cssPropertyNameCache[property]) {
			cssPropertyNameCache[property] = camelCase(property);
		}

		property   = cssPropertyNameCache[property];
		styleValue = elmStyle[property];

		// Add an exception for text-align
		if ('textAlign' === property) {
			styleValue = styleValue || css(elm, property);

			if (css(elm.parentNode, property) === styleValue ||
				css(elm, 'display') !== 'block' || is(elm, 'hr,th')) {
				return '';
			}
		}

		return styleValue;
	}

	/**
	 * Tests if an element has a style.
	 *
	 * If values are specified it will check that the styles value
	 * matches one of the values
	 *
	 * @param  {HTMLElement} elm
	 * @param  {string} property
	 * @param  {string|array} [values]
	 * @return {boolean}
	 */
	function hasStyle(elm, property, values) {
		var styleValue = getStyle(elm, property);

		if (!styleValue) {
			return false;
		}

		return !values || styleValue === values ||
			(Array.isArray(values) && values.indexOf(styleValue) > -1);
	}

	/**
	 * Returns true if both nodes have the same number of inline styles and all the
	 * inline styles have matching values
	 *
	 * @param {HTMLElement} nodeA
	 * @param {HTMLElement} nodeB
	 * @returns {boolean}
	 */
	function stylesMatch(nodeA, nodeB) {
		var i = nodeA.style.length;
		if (i !== nodeB.style.length) {
			return false;
		}

		while (i--) {
			var prop = nodeA.style[i];
			if (nodeA.style[prop] !== nodeB.style[prop]) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Returns true if both nodes have the same number of attributes and all the
	 * attribute values match
	 *
	 * @param {HTMLElement} nodeA
	 * @param {HTMLElement} nodeB
	 * @returns {boolean}
	 */
	function attributesMatch(nodeA, nodeB) {
		var i = nodeA.attributes.length;
		if (i !== nodeB.attributes.length) {
			return false;
		}

		while (i--) {
			var prop = nodeA.attributes[i];
			var notMatches = prop.name === 'style' ?
				!stylesMatch(nodeA, nodeB) :
				prop.value !== attr(nodeB, prop.name);

			if (notMatches) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Removes an element placing its children in its place
	 *
	 * @param {HTMLElement} node
	 */
	function removeKeepChildren(node) {
		while (node.firstChild) {
			insertBefore(node.firstChild, node);
		}

		remove(node);
	}

	/**
	 * Merges inline styles and tags with parents where possible
	 *
	 * @param {Node} node
	 * @since 3.1.0
	 */
	function merge(node) {
		if (node.nodeType !== ELEMENT_NODE) {
			return;
		}

		var parent = node.parentNode;
		var tagName = node.tagName;
		var mergeTags = /B|STRONG|EM|SPAN|FONT/;

		// Merge children (in reverse as children can be removed)
		var i = node.childNodes.length;
		while (i--) {
			merge(node.childNodes[i]);
		}

		// Should only merge inline tags
		if (!isInline(node)) {
			return;
		}

		// Remove any inline styles that match the parent style
		i = node.style.length;
		while (i--) {
			var prop = node.style[i];
			if (css(parent, prop) === css(node, prop)) {
				node.style.removeProperty(prop);
			}
		}

		// Can only remove / merge tags if no inline styling left.
		// If there is any inline style left then it means it at least partially
		// doesn't match the parent style so must stay
		if (!node.style.length) {
			removeAttr(node, 'style');

			// Remove font attributes if match parent
			if (tagName === 'FONT') {
				if (css(node, 'fontFamily').toLowerCase() ===
					css(parent, 'fontFamily').toLowerCase()) {
					removeAttr(node, 'face');
				}

				if (css(node, 'color') === css(parent, 'color')) {
					removeAttr(node, 'color');
				}

				if (css(node, 'fontSize') === css(parent, 'fontSize')) {
					removeAttr(node, 'size');
				}
			}

			// Spans and font tags with no attributes can be safely removed
			if (!node.attributes.length && /SPAN|FONT/.test(tagName)) {
				removeKeepChildren(node);
			} else if (mergeTags.test(tagName)) {
				var isBold = /B|STRONG/.test(tagName);
				var isItalic = tagName === 'EM';

				while (parent && isInline(parent) &&
					(!isBold || /bold|700/i.test(css(parent, 'fontWeight'))) &&
					(!isItalic || css(parent, 'fontStyle') === 'italic')) {

					// Remove if parent match
					if ((parent.tagName === tagName ||
						(isBold && /B|STRONG/.test(parent.tagName))) &&
						attributesMatch(parent, node)) {
						removeKeepChildren(node);
						break;
					}

					parent = parent.parentNode;
				}
			}
		}

		// Merge siblings if attributes, including inline styles, match
		var next = node.nextSibling;
		if (next && next.tagName === tagName && attributesMatch(next, node)) {
			appendChild(node, next);
			removeKeepChildren(next);
		}
	}

	/**
	 * Default options for SCEditor
	 * @type {Object}
	 */
	var defaultOptions = {
		/** @lends jQuery.sceditor.defaultOptions */
		/**
		 * Toolbar buttons order and groups. Should be comma separated and
		 * have a bar | to separate groups
		 *
		 * @type {string}
		 */
		toolbar: 'bold,italic,underline,strike,subscript,superscript|' +
			'left,center,right,justify|font,size,color,removeformat|' +
			'cut,copy,pastetext|bulletlist,orderedlist,indent,outdent|' +
			'table|code,quote|horizontalrule,image,email,link,unlink|' +
			'emoticon,youtube,date,time|ltr,rtl|print,maximize,source',

		/**
		 * Comma separated list of commands to excludes from the toolbar
		 *
		 * @type {string}
		 */
		toolbarExclude: null,

		/**
		 * Stylesheet to include in the WYSIWYG editor. This is what will style
		 * the WYSIWYG elements
		 *
		 * @type {string}
		 */
		style: 'jquery.sceditor.default.css',

		/**
		 * Comma separated list of fonts for the font selector
		 *
		 * @type {string}
		 */
		fonts: 'Arial,Arial Black,Comic Sans MS,Courier New,Georgia,Impact,' +
			'Sans-serif,Serif,Times New Roman,Trebuchet MS,Verdana',

		/**
		 * Colors should be comma separated and have a bar | to signal a new
		 * column.
		 *
		 * If null the colors will be auto generated.
		 *
		 * @type {string}
		 */
		colors: '#000000,#44B8FF,#1E92F7,#0074D9,#005DC2,#00369B,#b3d5f4|' +
				'#444444,#C3FFFF,#9DF9FF,#7FDBFF,#68C4E8,#419DC1,#d9f4ff|' +
				'#666666,#72FF84,#4CEA5E,#2ECC40,#17B529,#008E02,#c0f0c6|' +
				'#888888,#FFFF44,#FFFA1E,#FFDC00,#E8C500,#C19E00,#fff5b3|' +
				'#aaaaaa,#FFC95F,#FFA339,#FF851B,#E86E04,#C14700,#ffdbbb|' +
				'#cccccc,#FF857A,#FF5F54,#FF4136,#E82A1F,#C10300,#ffc6c3|' +
				'#eeeeee,#FF56FF,#FF30DC,#F012BE,#D900A7,#B20080,#fbb8ec|' +
				'#ffffff,#F551FF,#CF2BE7,#B10DC9,#9A00B2,#9A00B2,#e8b6ef',

		/**
		 * The locale to use.
		 * @type {string}
		 */
		locale: attr(document.documentElement, 'lang') || 'en',

		/**
		 * The Charset to use
		 * @type {string}
		 */
		charset: 'utf-8',

		/**
		 * Compatibility mode for emoticons.
		 *
		 * Helps if you have emoticons such as :/ which would put an emoticon
		 * inside http://
		 *
		 * This mode requires emoticons to be surrounded by whitespace or end of
		 * line chars. This mode has limited As You Type emoticon conversion
		 * support. It will not replace AYT for end of line chars, only
		 * emoticons surrounded by whitespace. They will still be replaced
		 * correctly when loaded just not AYT.
		 *
		 * @type {boolean}
		 */
		emoticonsCompat: false,

		/**
		 * If to enable emoticons. Can be changes at runtime using the
		 * emoticons() method.
		 *
		 * @type {boolean}
		 * @since 1.4.2
		 */
		emoticonsEnabled: true,

		/**
		 * Emoticon root URL
		 *
		 * @type {string}
		 */
		emoticonsRoot: '',
		emoticons: {
			dropdown: {
				':)': 'emoticons/smile.png',
				':angel:': 'emoticons/angel.png',
				':angry:': 'emoticons/angry.png',
				'8-)': 'emoticons/cool.png',
				':\'(': 'emoticons/cwy.png',
				':ermm:': 'emoticons/ermm.png',
				':D': 'emoticons/grin.png',
				'<3': 'emoticons/heart.png',
				':(': 'emoticons/sad.png',
				':O': 'emoticons/shocked.png',
				':P': 'emoticons/tongue.png',
				';)': 'emoticons/wink.png'
			},
			more: {
				':alien:': 'emoticons/alien.png',
				':blink:': 'emoticons/blink.png',
				':blush:': 'emoticons/blush.png',
				':cheerful:': 'emoticons/cheerful.png',
				':devil:': 'emoticons/devil.png',
				':dizzy:': 'emoticons/dizzy.png',
				':getlost:': 'emoticons/getlost.png',
				':happy:': 'emoticons/happy.png',
				':kissing:': 'emoticons/kissing.png',
				':ninja:': 'emoticons/ninja.png',
				':pinch:': 'emoticons/pinch.png',
				':pouty:': 'emoticons/pouty.png',
				':sick:': 'emoticons/sick.png',
				':sideways:': 'emoticons/sideways.png',
				':silly:': 'emoticons/silly.png',
				':sleeping:': 'emoticons/sleeping.png',
				':unsure:': 'emoticons/unsure.png',
				':woot:': 'emoticons/w00t.png',
				':wassat:': 'emoticons/wassat.png'
			},
			hidden: {
				':whistling:': 'emoticons/whistling.png',
				':love:': 'emoticons/wub.png'
			}
		},

		/**
		 * Width of the editor. Set to null for automatic with
		 *
		 * @type {?number}
		 */
		width: null,

		/**
		 * Height of the editor including toolbar. Set to null for automatic
		 * height
		 *
		 * @type {?number}
		 */
		height: null,

		/**
		 * If to allow the editor to be resized
		 *
		 * @type {boolean}
		 */
		resizeEnabled: true,

		/**
		 * Min resize to width, set to null for half textarea width or -1 for
		 * unlimited
		 *
		 * @type {?number}
		 */
		resizeMinWidth: null,
		/**
		 * Min resize to height, set to null for half textarea height or -1 for
		 * unlimited
		 *
		 * @type {?number}
		 */
		resizeMinHeight: null,
		/**
		 * Max resize to height, set to null for double textarea height or -1
		 * for unlimited
		 *
		 * @type {?number}
		 */
		resizeMaxHeight: null,
		/**
		 * Max resize to width, set to null for double textarea width or -1 for
		 * unlimited
		 *
		 * @type {?number}
		 */
		resizeMaxWidth: null,
		/**
		 * If resizing by height is enabled
		 *
		 * @type {boolean}
		 */
		resizeHeight: true,
		/**
		 * If resizing by width is enabled
		 *
		 * @type {boolean}
		 */
		resizeWidth: true,

		/**
		 * Date format, will be overridden if locale specifies one.
		 *
		 * The words year, month and day will be replaced with the users current
		 * year, month and day.
		 *
		 * @type {string}
		 */
		dateFormat: 'year-month-day',

		/**
		 * Element to inset the toolbar into.
		 *
		 * @type {HTMLElement}
		 */
		toolbarContainer: null,

		/**
		 * If to enable paste filtering. This is currently experimental, please
		 * report any issues.
		 *
		 * @type {boolean}
		 */
		enablePasteFiltering: false,

		/**
		 * If to completely disable pasting into the editor
		 *
		 * @type {boolean}
		 */
		disablePasting: false,

		/**
		 * If the editor is read only.
		 *
		 * @type {boolean}
		 */
		readOnly: false,

		/**
		 * If to set the editor to right-to-left mode.
		 *
		 * If set to null the direction will be automatically detected.
		 *
		 * @type {boolean}
		 */
		rtl: false,

		/**
		 * If to auto focus the editor on page load
		 *
		 * @type {boolean}
		 */
		autofocus: false,

		/**
		 * If to auto focus the editor to the end of the content
		 *
		 * @type {boolean}
		 */
		autofocusEnd: true,

		/**
		 * If to auto expand the editor to fix the content
		 *
		 * @type {boolean}
		 */
		autoExpand: false,

		/**
		 * If to auto update original textbox on blur
		 *
		 * @type {boolean}
		 */
		autoUpdate: false,

		/**
		 * If to enable the browsers built in spell checker
		 *
		 * @type {boolean}
		 */
		spellcheck: true,

		/**
		 * If to run the source editor when there is no WYSIWYG support. Only
		 * really applies to mobile OS's.
		 *
		 * @type {boolean}
		 */
		runWithoutWysiwygSupport: false,

		/**
		 * If to load the editor in source mode and still allow switching
		 * between WYSIWYG and source mode
		 *
		 * @type {boolean}
		 */
		startInSourceMode: false,

		/**
		 * Optional ID to give the editor.
		 *
		 * @type {string}
		 */
		id: null,

		/**
		 * Comma separated list of plugins
		 *
		 * @type {string}
		 */
		plugins: '',

		/**
		 * z-index to set the editor container to. Needed for jQuery UI dialog.
		 *
		 * @type {?number}
		 */
		zIndex: null,

		/**
		 * If to trim the BBCode. Removes any spaces at the start and end of the
		 * BBCode string.
		 *
		 * @type {boolean}
		 */
		bbcodeTrim: false,

		/**
		 * If to disable removing block level elements by pressing backspace at
		 * the start of them
		 *
		 * @type {boolean}
		 */
		disableBlockRemove: false,

		/**
		 * Array of allowed URL (should be either strings or regex) for iframes.
		 *
		 * If it's a string then iframes where the start of the src matches the
		 * specified string will be allowed.
		 *
		 * If it's a regex then iframes where the src matches the regex will be
		 * allowed.
		 *
		 * @type {Array}
		 */
		allowedIframeUrls: [],

		/**
		 * BBCode parser options, only applies if using the editor in BBCode
		 * mode.
		 *
		 * See SCEditor.BBCodeParser.defaults for list of valid options
		 *
		 * @type {Object}
		 */
		parserOptions: { },

		/**
		 * CSS that will be added to the to dropdown menu (eg. z-index)
		 *
		 * @type {Object}
		 */
		dropDownCss: { }
	};

	// Must start with a valid scheme
	// 		^
	// Schemes that are considered safe
	// 		(https?|s?ftp|mailto|spotify|skype|ssh|teamspeak|tel):|
	// Relative schemes (//:) are considered safe
	// 		(\\/\\/)|
	// Image data URI's are considered safe
	// 		data:image\\/(png|bmp|gif|p?jpe?g);
	var VALID_SCHEME_REGEX =
		/^(https?|s?ftp|mailto|spotify|skype|ssh|teamspeak|tel):|(\/\/)|data:image\/(png|bmp|gif|p?jpe?g);/i;

	/**
	 * Escapes a string so it's safe to use in regex
	 *
	 * @param {string} str
	 * @return {string}
	 */
	function regex(str) {
		return str.replace(/([\-.*+?^=!:${}()|\[\]\/\\])/g, '\\$1');
	}
	/**
	 * Escapes all HTML entities in a string
	 *
	 * If noQuotes is set to false, all single and double
	 * quotes will also be escaped
	 *
	 * @param {string} str
	 * @param {boolean} [noQuotes=true]
	 * @return {string}
	 * @since 1.4.1
	 */
	function entities(str, noQuotes) {
		if (!str) {
			return str;
		}

		var replacements = {
			'&': '&amp;',
			'<': '&lt;',
			'>': '&gt;',
			'  ': '&nbsp; ',
			'\r\n': '<br />',
			'\r': '<br />',
			'\n': '<br />'
		};

		if (noQuotes !== false) {
			replacements['"']  = '&#34;';
			replacements['\''] = '&#39;';
			replacements['`']  = '&#96;';
		}

		str = str.replace(/ {2}|\r\n|[&<>\r\n'"`]/g, function (match) {
			return replacements[match] || match;
		});

		return str;
	}
	/**
	 * Escape URI scheme.
	 *
	 * Appends the current URL to a url if it has a scheme that is not:
	 *
	 * http
	 * https
	 * sftp
	 * ftp
	 * mailto
	 * spotify
	 * skype
	 * ssh
	 * teamspeak
	 * tel
	 * //
	 * data:image/(png|jpeg|jpg|pjpeg|bmp|gif);
	 *
	 * **IMPORTANT**: This does not escape any HTML in a url, for
	 * that use the escape.entities() method.
	 *
	 * @param  {string} url
	 * @return {string}
	 * @since 1.4.5
	 */
	function uriScheme(url) {
		var	path,
			// If there is a : before a / then it has a scheme
			hasScheme = /^[^\/]*:/i,
			location = window.location;

		// Has no scheme or a valid scheme
		if ((!url || !hasScheme.test(url)) || VALID_SCHEME_REGEX.test(url)) {
			return url;
		}

		path = location.pathname.split('/');
		path.pop();

		return location.protocol + '//' +
			location.host +
			path.join('/') + '/' +
			url;
	}

	/**
	 * HTML templates used by the editor and default commands
	 * @type {Object}
	 * @private
	 */
	var _templates = {
		html:
			'<!DOCTYPE html>' +
			'<html{attrs}>' +
				'<head>' +
					'<meta http-equiv="Content-Type" ' +
						'content="text/html;charset={charset}" />' +
					'<link rel="stylesheet" type="text/css" href="{style}" />' +
				'</head>' +
				'<body contenteditable="true" {spellcheck}><p></p></body>' +
			'</html>',

		toolbarButton: '<a class="sceditor-button sceditor-button-{name}" ' +
			'data-sceditor-command="{name}" unselectable="on">' +
			'<div unselectable="on">{dispName}</div></a>',

		emoticon: '<img src="{url}" data-sceditor-emoticon="{key}" ' +
			'alt="{key}" title="{tooltip}" />',

		fontOpt: '<a class="sceditor-font-option" href="#" ' +
			'data-font="{font}"><font face="{font}">{font}</font></a>',

		sizeOpt: '<a class="sceditor-fontsize-option" data-size="{size}" ' +
			'href="#"><font size="{size}">{size}</font></a>',

		pastetext:
			'<div><label for="txt">{label}</label> ' +
				'<textarea cols="20" rows="7" id="txt"></textarea></div>' +
				'<div><input type="button" class="button" value="{insert}" />' +
			'</div>',

		table:
			'<div><label for="rows">{rows}</label><input type="text" ' +
				'id="rows" value="2" /></div>' +
			'<div><label for="cols">{cols}</label><input type="text" ' +
				'id="cols" value="2" /></div>' +
			'<div><input type="button" class="button" value="{insert}"' +
				' /></div>',

		image:
			'<div><label for="image">{url}</label> ' +
				'<input type="text" id="image" dir="ltr" placeholder="https://" /></div>' +
			'<div><label for="width">{width}</label> ' +
				'<input type="text" id="width" size="2" dir="ltr" /></div>' +
			'<div><label for="height">{height}</label> ' +
				'<input type="text" id="height" size="2" dir="ltr" /></div>' +
			'<div><input type="button" class="button" value="{insert}" />' +
				'</div>',

		email:
			'<div><label for="email">{label}</label> ' +
				'<input type="text" id="email" dir="ltr" /></div>' +
			'<div><label for="des">{desc}</label> ' +
				'<input type="text" id="des" /></div>' +
			'<div><input type="button" class="button" value="{insert}" />' +
				'</div>',

		link:
			'<div><label for="link">{url}</label> ' +
				'<input type="text" id="link" dir="ltr" placeholder="https://" /></div>' +
			'<div><label for="des">{desc}</label> ' +
				'<input type="text" id="des" /></div>' +
			'<div><input type="button" class="button" value="{ins}" /></div>',

		youtubeMenu:
			'<div><label for="link">{label}</label> ' +
				'<input type="text" id="link" dir="ltr" placeholder="https://" /></div>' +
			'<div><input type="button" class="button" value="{insert}" />' +
				'</div>',

		youtube:
			'<iframe width="560" height="315" frameborder="0" allowfullscreen ' +
			'src="https://www.youtube-nocookie.com/embed/{id}?wmode=opaque&start={time}" ' +
			'data-youtube-id="{id}"></iframe>'
	};

	/**
	 * Replaces any params in a template with the passed params.
	 *
	 * If createHtml is passed it will return a DocumentFragment
	 * containing the parsed template.
	 *
	 * @param {string} name
	 * @param {Object} [params]
	 * @param {boolean} [createHtml]
	 * @returns {string|DocumentFragment}
	 * @private
	 */
	function _tmpl (name, params, createHtml) {
		var template = _templates[name];

		Object.keys(params).forEach(function (name) {
			template = template.replace(
				new RegExp(regex('{' + name + '}'), 'g'), params[name]
			);
		});

		if (createHtml) {
			template = parseHTML(template);
		}

		return template;
	}

	/**
	 * Fixes a bug in FF where it sometimes wraps
	 * new lines in their own list item.
	 * See issue #359
	 */
	function fixFirefoxListBug(editor) {
		// Only apply to Firefox as will break other browsers.
		if ('mozHidden' in document) {
			var node = editor.getBody();
			var next;

			while (node) {
				next = node;

				if (next.firstChild) {
					next = next.firstChild;
				} else {

					while (next && !next.nextSibling) {
						next = next.parentNode;
					}

					if (next) {
						next = next.nextSibling;
					}
				}

				if (node.nodeType === 3 && /[\n\r\t]+/.test(node.nodeValue)) {
					// Only remove if newlines are collapsed
					if (!/^pre/.test(css(node.parentNode, 'whiteSpace'))) {
						remove(node);
					}
				}

				node = next;
			}
		}
	}


	/**
	 * Map of all the commands for SCEditor
	 * @type {Object}
	 * @name commands
	 * @memberOf jQuery.sceditor
	 */
	var defaultCmds = {
		// START_COMMAND: Bold
		bold: {
			exec: 'bold',
			tooltip: 'Bold',
			shortcut: 'Ctrl+B'
		},
		// END_COMMAND
		// START_COMMAND: Italic
		italic: {
			exec: 'italic',
			tooltip: 'Italic',
			shortcut: 'Ctrl+I'
		},
		// END_COMMAND
		// START_COMMAND: Underline
		underline: {
			exec: 'underline',
			tooltip: 'Underline',
			shortcut: 'Ctrl+U'
		},
		// END_COMMAND
		// START_COMMAND: Strikethrough
		strike: {
			exec: 'strikethrough',
			tooltip: 'Strikethrough'
		},
		// END_COMMAND
		// START_COMMAND: Subscript
		subscript: {
			exec: 'subscript',
			tooltip: 'Subscript'
		},
		// END_COMMAND
		// START_COMMAND: Superscript
		superscript: {
			exec: 'superscript',
			tooltip: 'Superscript'
		},
		// END_COMMAND

		// START_COMMAND: Left
		left: {
			state: function (node) {
				if (node && node.nodeType === 3) {
					node = node.parentNode;
				}

				if (node) {
					var isLtr = css(node, 'direction') === 'ltr';
					var align = css(node, 'textAlign');

					// Can be -moz-left
					return /left/.test(align) ||
						align === (isLtr ? 'start' : 'end');
				}
			},
			exec: 'justifyleft',
			tooltip: 'Align left'
		},
		// END_COMMAND
		// START_COMMAND: Centre
		center: {
			exec: 'justifycenter',
			tooltip: 'Center'
		},
		// END_COMMAND
		// START_COMMAND: Right
		right: {
			state: function (node) {
				if (node && node.nodeType === 3) {
					node = node.parentNode;
				}

				if (node) {
					var isLtr = css(node, 'direction') === 'ltr';
					var align = css(node, 'textAlign');

					// Can be -moz-right
					return /right/.test(align) ||
						align === (isLtr ? 'end' : 'start');
				}
			},
			exec: 'justifyright',
			tooltip: 'Align right'
		},
		// END_COMMAND
		// START_COMMAND: Justify
		justify: {
			exec: 'justifyfull',
			tooltip: 'Justify'
		},
		// END_COMMAND

		// START_COMMAND: Font
		font: {
			_dropDown: function (editor, caller, callback) {
				var	content = createElement('div');

				on(content, 'click', 'a', function (e) {
					callback(data(this, 'font'));
					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.opts.fonts.split(',').forEach(function (font) {
					appendChild(content, _tmpl('fontOpt', {
						font: font
					}, true));
				});

				editor.createDropDown(caller, 'font-picker', content);
			},
			exec: function (caller) {
				var editor = this;

				defaultCmds.font._dropDown(editor, caller, function (fontName) {
					editor.execCommand('fontname', fontName);
				});
			},
			tooltip: 'Font Name'
		},
		// END_COMMAND
		// START_COMMAND: Size
		size: {
			_dropDown: function (editor, caller, callback) {
				var	content = createElement('div');

				on(content, 'click', 'a', function (e) {
					callback(data(this, 'size'));
					editor.closeDropDown(true);
					e.preventDefault();
				});

				for (var i = 1; i <= 7; i++) {
					appendChild(content, _tmpl('sizeOpt', {
						size: i
					}, true));
				}

				editor.createDropDown(caller, 'fontsize-picker', content);
			},
			exec: function (caller) {
				var editor = this;

				defaultCmds.size._dropDown(editor, caller, function (fontSize) {
					editor.execCommand('fontsize', fontSize);
				});
			},
			tooltip: 'Font Size'
		},
		// END_COMMAND
		// START_COMMAND: Colour
		color: {
			_dropDown: function (editor, caller, callback) {
				var	content = createElement('div'),
					html    = '',
					cmd     = defaultCmds.color;

				if (!cmd._htmlCache) {
					editor.opts.colors.split('|').forEach(function (column) {
						html += '<div class="sceditor-color-column">';

						column.split(',').forEach(function (color) {
							html +=
								'<a href="#" class="sceditor-color-option"' +
								' style="background-color: ' + color + '"' +
								' data-color="' + color + '"></a>';
						});

						html += '</div>';
					});

					cmd._htmlCache = html;
				}

				appendChild(content, parseHTML(cmd._htmlCache));

				on(content, 'click', 'a', function (e) {
					callback(data(this, 'color'));
					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'color-picker', content);
			},
			exec: function (caller) {
				var editor = this;

				defaultCmds.color._dropDown(editor, caller, function (color) {
					editor.execCommand('forecolor', color);
				});
			},
			tooltip: 'Font Color'
		},
		// END_COMMAND
		// START_COMMAND: Remove Format
		removeformat: {
			exec: 'removeformat',
			tooltip: 'Remove Formatting'
		},
		// END_COMMAND

		// START_COMMAND: Cut
		cut: {
			exec: 'cut',
			tooltip: 'Cut',
			errorMessage: 'Your browser does not allow the cut command. ' +
				'Please use the keyboard shortcut Ctrl/Cmd-X'
		},
		// END_COMMAND
		// START_COMMAND: Copy
		copy: {
			exec: 'copy',
			tooltip: 'Copy',
			errorMessage: 'Your browser does not allow the copy command. ' +
				'Please use the keyboard shortcut Ctrl/Cmd-C'
		},
		// END_COMMAND
		// START_COMMAND: Paste
		paste: {
			exec: 'paste',
			tooltip: 'Paste',
			errorMessage: 'Your browser does not allow the paste command. ' +
				'Please use the keyboard shortcut Ctrl/Cmd-V'
		},
		// END_COMMAND
		// START_COMMAND: Paste Text
		pastetext: {
			exec: function (caller) {
				var	val,
					content = createElement('div'),
					editor  = this;

				appendChild(content, _tmpl('pastetext', {
					label: editor._(
						'Paste your text inside the following box:'
					),
					insert: editor._('Insert')
				}, true));

				on(content, 'click', '.button', function (e) {
					val = find(content, '#txt')[0].value;

					if (val) {
						editor.wysiwygEditorInsertText(val);
					}

					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'pastetext', content);
			},
			tooltip: 'Paste Text'
		},
		// END_COMMAND
		// START_COMMAND: Bullet List
		bulletlist: {
			exec: function () {
				fixFirefoxListBug(this);
				this.execCommand('insertunorderedlist');
			},
			tooltip: 'Bullet list'
		},
		// END_COMMAND
		// START_COMMAND: Ordered List
		orderedlist: {
			exec: function () {
				fixFirefoxListBug(this);
				this.execCommand('insertorderedlist');
			},
			tooltip: 'Numbered list'
		},
		// END_COMMAND
		// START_COMMAND: Indent
		indent: {
			state: function (parent, firstBlock) {
				// Only works with lists, for now
				var	range, startParent, endParent;

				if (is(firstBlock, 'li')) {
					return 0;
				}

				if (is(firstBlock, 'ul,ol,menu')) {
					// if the whole list is selected, then this must be
					// invalidated because the browser will place a
					// <blockquote> there
					range = this.getRangeHelper().selectedRange();

					startParent = range.startContainer.parentNode;
					endParent   = range.endContainer.parentNode;

					// TODO: could use nodeType for this?
					// Maybe just check the firstBlock contains both the start
					//and end containers

					// Select the tag, not the textNode
					// (that's why the parentNode)
					if (startParent !==
						startParent.parentNode.firstElementChild ||
						// work around a bug in FF
						(is(endParent, 'li') && endParent !==
							endParent.parentNode.lastElementChild)) {
						return 0;
					}
				}

				return -1;
			},
			exec: function () {
				var editor = this,
					block = editor.getRangeHelper().getFirstBlockParent();

				editor.focus();

				// An indent system is quite complicated as there are loads
				// of complications and issues around how to indent text
				// As default, let's just stay with indenting the lists,
				// at least, for now.
				if (closest(block, 'ul,ol,menu')) {
					editor.execCommand('indent');
				}
			},
			tooltip: 'Add indent'
		},
		// END_COMMAND
		// START_COMMAND: Outdent
		outdent: {
			state: function (parents, firstBlock) {
				return closest(firstBlock, 'ul,ol,menu') ? 0 : -1;
			},
			exec: function () {
				var	block = this.getRangeHelper().getFirstBlockParent();
				if (closest(block, 'ul,ol,menu')) {
					this.execCommand('outdent');
				}
			},
			tooltip: 'Remove one indent'
		},
		// END_COMMAND

		// START_COMMAND: Table
		table: {
			exec: function (caller) {
				var	editor  = this,
					content = createElement('div');

				appendChild(content, _tmpl('table', {
					rows: editor._('Rows:'),
					cols: editor._('Cols:'),
					insert: editor._('Insert')
				}, true));

				on(content, 'click', '.button', function (e) {
					var	rows = Number(find(content, '#rows')[0].value),
						cols = Number(find(content, '#cols')[0].value),
						html = '<table>';

					if (rows > 0 && cols > 0) {
						html += Array(rows + 1).join(
							'<tr>' +
								Array(cols + 1).join(
									'<td><br /></td>'
								) +
							'</tr>'
						);

						html += '</table>';

						editor.wysiwygEditorInsertHtml(html);
						editor.closeDropDown(true);
						e.preventDefault();
					}
				});

				editor.createDropDown(caller, 'inserttable', content);
			},
			tooltip: 'Insert a table'
		},
		// END_COMMAND

		// START_COMMAND: Horizontal Rule
		horizontalrule: {
			exec: 'inserthorizontalrule',
			tooltip: 'Insert a horizontal rule'
		},
		// END_COMMAND

		// START_COMMAND: Code
		code: {
			exec: function () {
				this.wysiwygEditorInsertHtml(
					'<code>',
					'<br /></code>'
				);
			},
			tooltip: 'Code'
		},
		// END_COMMAND

		// START_COMMAND: Image
		image: {
			_dropDown: function (editor, caller, selected, cb) {
				var	content = createElement('div');

				appendChild(content, _tmpl('image', {
					url: editor._('URL:'),
					width: editor._('Width (optional):'),
					height: editor._('Height (optional):'),
					insert: editor._('Insert')
				}, true));


				var	urlInput = find(content, '#image')[0];

				urlInput.value = selected;

				on(content, 'click', '.button', function (e) {
					if (urlInput.value) {
						cb(
							urlInput.value,
							find(content, '#width')[0].value,
							find(content, '#height')[0].value
						);
					}

					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'insertimage', content);
			},
			exec: function (caller) {
				var	editor  = this;

				defaultCmds.image._dropDown(
					editor,
					caller,
					'',
					function (url, width, height) {
						var attrs  = '';

						if (width) {
							attrs += ' width="' + parseInt(width, 10) + '"';
						}

						if (height) {
							attrs += ' height="' + parseInt(height, 10) + '"';
						}

						attrs += ' src="' + entities(url) + '"';

						editor.wysiwygEditorInsertHtml(
							'<img' + attrs + ' />'
						);
					}
				);
			},
			tooltip: 'Insert an image'
		},
		// END_COMMAND

		// START_COMMAND: E-mail
		email: {
			_dropDown: function (editor, caller, cb) {
				var	content = createElement('div');

				appendChild(content, _tmpl('email', {
					label: editor._('E-mail:'),
					desc: editor._('Description (optional):'),
					insert: editor._('Insert')
				}, true));

				on(content, 'click', '.button', function (e) {
					var email = find(content, '#email')[0].value;

					if (email) {
						cb(email, find(content, '#des')[0].value);
					}

					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'insertemail', content);
			},
			exec: function (caller) {
				var	editor  = this;

				defaultCmds.email._dropDown(
					editor,
					caller,
					function (email, text) {
						if (!editor.getRangeHelper().selectedHtml() || text) {
							editor.wysiwygEditorInsertHtml(
								'<a href="' +
								'mailto:' + entities(email) + '">' +
									entities((text || email)) +
								'</a>'
							);
						} else {
							editor.execCommand('createlink', 'mailto:' + email);
						}
					}
				);
			},
			tooltip: 'Insert an email'
		},
		// END_COMMAND

		// START_COMMAND: Link
		link: {
			_dropDown: function (editor, caller, cb) {
				var content = createElement('div');

				appendChild(content, _tmpl('link', {
					url: editor._('URL:'),
					desc: editor._('Description (optional):'),
					ins: editor._('Insert')
				}, true));

				var linkInput = find(content, '#link')[0];

				function insertUrl(e) {
					if (linkInput.value) {
						cb(linkInput.value, find(content, '#des')[0].value);
					}

					editor.closeDropDown(true);
					e.preventDefault();
				}

				on(content, 'click', '.button', insertUrl);
				on(content, 'keypress', function (e) {
					// 13 = enter key
					if (e.which === 13 && linkInput.value) {
						insertUrl(e);
					}
				}, EVENT_CAPTURE);

				editor.createDropDown(caller, 'insertlink', content);
			},
			exec: function (caller) {
				var editor = this;

				defaultCmds.link._dropDown(editor, caller, function (url, text) {
					if (text || !editor.getRangeHelper().selectedHtml()) {
						editor.wysiwygEditorInsertHtml(
							'<a href="' + entities(url) + '">' +
								entities(text || url) +
							'</a>'
						);
					} else {
						editor.execCommand('createlink', url);
					}
				});
			},
			tooltip: 'Insert a link'
		},
		// END_COMMAND

		// START_COMMAND: Unlink
		unlink: {
			state: function () {
				return closest(this.currentNode(), 'a') ? 0 : -1;
			},
			exec: function () {
				var anchor = closest(this.currentNode(), 'a');

				if (anchor) {
					while (anchor.firstChild) {
						insertBefore(anchor.firstChild, anchor);
					}

					remove(anchor);
				}
			},
			tooltip: 'Unlink'
		},
		// END_COMMAND


		// START_COMMAND: Quote
		quote: {
			exec: function (caller, html, author) {
				var	before = '<blockquote>',
					end    = '</blockquote>';

				// if there is HTML passed set end to null so any selected
				// text is replaced
				if (html) {
					author = (author ? '<cite>' +
						entities(author) +
					'</cite>' : '');
					before = before + author + html + end;
					end    = null;
				// if not add a newline to the end of the inserted quote
				} else if (this.getRangeHelper().selectedHtml() === '') {
					end = '<br />' + end;
				}

				this.wysiwygEditorInsertHtml(before, end);
			},
			tooltip: 'Insert a Quote'
		},
		// END_COMMAND

		// START_COMMAND: Emoticons
		emoticon: {
			exec: function (caller) {
				var editor = this;

				var createContent = function (includeMore) {
					var	moreLink,
						opts            = editor.opts,
						emoticonsRoot   = opts.emoticonsRoot || '',
						emoticonsCompat = opts.emoticonsCompat,
						rangeHelper     = editor.getRangeHelper(),
						startSpace      = emoticonsCompat &&
							rangeHelper.getOuterText(true, 1) !== ' ' ? ' ' : '',
						endSpace        = emoticonsCompat &&
							rangeHelper.getOuterText(false, 1) !== ' ' ? ' ' : '',
						content         = createElement('div'),
						line            = createElement('div'),
						perLine         = 0,
						emoticons       = extend(
							{},
							opts.emoticons.dropdown,
							includeMore ? opts.emoticons.more : {}
						);

					appendChild(content, line);

					perLine = Math.sqrt(Object.keys(emoticons).length);

					on(content, 'click', 'img', function (e) {
						editor.insert(startSpace + attr(this, 'alt') + endSpace,
							null, false).closeDropDown(true);

						e.preventDefault();
					});

					each(emoticons, function (code, emoticon) {
						appendChild(line, createElement('img', {
							src: emoticonsRoot + (emoticon.url || emoticon),
							alt: code,
							title: emoticon.tooltip || code
						}));

						if (line.children.length >= perLine) {
							line = createElement('div');
							appendChild(content, line);
						}
					});

					if (!includeMore && opts.emoticons.more) {
						moreLink = createElement('a', {
							className: 'sceditor-more'
						});

						appendChild(moreLink,
							document.createTextNode(editor._('More')));

						on(moreLink, 'click', function (e) {
							editor.createDropDown(
								caller, 'more-emoticons', createContent(true)
							);

							e.preventDefault();
						});

						appendChild(content, moreLink);
					}

					return content;
				};

				editor.createDropDown(caller, 'emoticons', createContent(false));
			},
			txtExec: function (caller) {
				defaultCmds.emoticon.exec.call(this, caller);
			},
			tooltip: 'Insert an emoticon'
		},
		// END_COMMAND

		// START_COMMAND: YouTube
		youtube: {
			_dropDown: function (editor, caller, callback) {
				var	content = createElement('div');

				appendChild(content, _tmpl('youtubeMenu', {
					label: editor._('Video URL:'),
					insert: editor._('Insert')
				}, true));

				on(content, 'click', '.button', function (e) {
					var val = find(content, '#link')[0].value;
					var idMatch = val.match(/(?:v=|v\/|embed\/|youtu.be\/)?([a-zA-Z0-9_-]{11})/);
					var timeMatch = val.match(/[&|?](?:star)?t=((\d+[hms]?){1,3})/);
					var time = 0;

					if (timeMatch) {
						each(timeMatch[1].split(/[hms]/), function (i, val) {
							if (val !== '') {
								time = (time * 60) + Number(val);
							}
						});
					}

					if (idMatch && /^[a-zA-Z0-9_\-]{11}$/.test(idMatch[1])) {
						callback(idMatch[1], time);
					}

					editor.closeDropDown(true);
					e.preventDefault();
				});

				editor.createDropDown(caller, 'insertlink', content);
			},
			exec: function (btn) {
				var editor = this;

				defaultCmds.youtube._dropDown(editor, btn, function (id, time) {
					editor.wysiwygEditorInsertHtml(_tmpl('youtube', {
						id: id,
						time: time
					}));
				});
			},
			tooltip: 'Insert a YouTube video'
		},
		// END_COMMAND

		// START_COMMAND: Date
		date: {
			_date: function (editor) {
				var	now   = new Date(),
					year  = now.getYear(),
					month = now.getMonth() + 1,
					day   = now.getDate();

				if (year < 2000) {
					year = 1900 + year;
				}

				if (month < 10) {
					month = '0' + month;
				}

				if (day < 10) {
					day = '0' + day;
				}

				return editor.opts.dateFormat
					.replace(/year/i, year)
					.replace(/month/i, month)
					.replace(/day/i, day);
			},
			exec: function () {
				this.insertText(defaultCmds.date._date(this));
			},
			txtExec: function () {
				this.insertText(defaultCmds.date._date(this));
			},
			tooltip: 'Insert current date'
		},
		// END_COMMAND

		// START_COMMAND: Time
		time: {
			_time: function () {
				var	now   = new Date(),
					hours = now.getHours(),
					mins  = now.getMinutes(),
					secs  = now.getSeconds();

				if (hours < 10) {
					hours = '0' + hours;
				}

				if (mins < 10) {
					mins = '0' + mins;
				}

				if (secs < 10) {
					secs = '0' + secs;
				}

				return hours + ':' + mins + ':' + secs;
			},
			exec: function () {
				this.insertText(defaultCmds.time._time());
			},
			txtExec: function () {
				this.insertText(defaultCmds.time._time());
			},
			tooltip: 'Insert current time'
		},
		// END_COMMAND


		// START_COMMAND: Ltr
		ltr: {
			state: function (parents, firstBlock) {
				return firstBlock && firstBlock.style.direction === 'ltr';
			},
			exec: function () {
				var	editor = this,
					rangeHelper = editor.getRangeHelper(),
					node = rangeHelper.getFirstBlockParent();

				editor.focus();

				if (!node || is(node, 'body')) {
					editor.execCommand('formatBlock', 'p');

					node  = rangeHelper.getFirstBlockParent();

					if (!node || is(node, 'body')) {
						return;
					}
				}

				var toggleValue = css(node, 'direction') === 'ltr' ? '' : 'ltr';
				css(node, 'direction', toggleValue);
			},
			tooltip: 'Left-to-Right'
		},
		// END_COMMAND

		// START_COMMAND: Rtl
		rtl: {
			state: function (parents, firstBlock) {
				return firstBlock && firstBlock.style.direction === 'rtl';
			},
			exec: function () {
				var	editor = this,
					rangeHelper = editor.getRangeHelper(),
					node = rangeHelper.getFirstBlockParent();

				editor.focus();

				if (!node || is(node, 'body')) {
					editor.execCommand('formatBlock', 'p');

					node = rangeHelper.getFirstBlockParent();

					if (!node || is(node, 'body')) {
						return;
					}
				}

				var toggleValue = css(node, 'direction') === 'rtl' ? '' : 'rtl';
				css(node, 'direction', toggleValue);
			},
			tooltip: 'Right-to-Left'
		},
		// END_COMMAND


		// START_COMMAND: Print
		print: {
			exec: 'print',
			tooltip: 'Print'
		},
		// END_COMMAND

		// START_COMMAND: Maximize
		maximize: {
			state: function () {
				return this.maximize();
			},
			exec: function () {
				this.maximize(!this.maximize());
				this.focus();
			},
			txtExec: function () {
				this.maximize(!this.maximize());
				this.focus();
			},
			tooltip: 'Maximize',
			shortcut: 'Ctrl+Shift+M'
		},
		// END_COMMAND

		// START_COMMAND: Source
		source: {
			state: function () {
				return this.sourceMode();
			},
			exec: function () {
				this.toggleSourceMode();
				this.focus();
			},
			txtExec: function () {
				this.toggleSourceMode();
				this.focus();
			},
			tooltip: 'View source',
			shortcut: 'Ctrl+Shift+S'
		},
		// END_COMMAND

		// this is here so that commands above can be removed
		// without having to remove the , after the last one.
		// Needed for IE.
		ignore: {}
	};

	var plugins = {};

	/**
	 * Plugin Manager class
	 * @class PluginManager
	 * @name PluginManager
	 */
	function PluginManager(thisObj) {
		/**
		 * Alias of this
		 *
		 * @private
		 * @type {Object}
		 */
		var base = this;

		/**
		 * Array of all currently registered plugins
		 *
		 * @type {Array}
		 * @private
		 */
		var registeredPlugins = [];


		/**
		 * Changes a signals name from "name" into "signalName".
		 *
		 * @param  {string} signal
		 * @return {string}
		 * @private
		 */
		var formatSignalName = function (signal) {
			return 'signal' + signal.charAt(0).toUpperCase() + signal.slice(1);
		};

		/**
		 * Calls handlers for a signal
		 *
		 * @see call()
		 * @see callOnlyFirst()
		 * @param  {Array}   args
		 * @param  {boolean} returnAtFirst
		 * @return {*}
		 * @private
		 */
		var callHandlers = function (args, returnAtFirst) {
			args = [].slice.call(args);

			var	idx, ret,
				signal = formatSignalName(args.shift());

			for (idx = 0; idx < registeredPlugins.length; idx++) {
				if (signal in registeredPlugins[idx]) {
					ret = registeredPlugins[idx][signal].apply(thisObj, args);

					if (returnAtFirst) {
						return ret;
					}
				}
			}
		};

		/**
		 * Calls all handlers for the passed signal
		 *
		 * @param  {string}    signal
		 * @param  {...string} args
		 * @function
		 * @name call
		 * @memberOf PluginManager.prototype
		 */
		base.call = function () {
			callHandlers(arguments, false);
		};

		/**
		 * Calls the first handler for a signal, and returns the
		 *
		 * @param  {string}    signal
		 * @param  {...string} args
		 * @return {*} The result of calling the handler
		 * @function
		 * @name callOnlyFirst
		 * @memberOf PluginManager.prototype
		 */
		base.callOnlyFirst = function () {
			return callHandlers(arguments, true);
		};

		/**
		 * Checks if a signal has a handler
		 *
		 * @param  {string} signal
		 * @return {boolean}
		 * @function
		 * @name hasHandler
		 * @memberOf PluginManager.prototype
		 */
		base.hasHandler = function (signal) {
			var i  = registeredPlugins.length;
			signal = formatSignalName(signal);

			while (i--) {
				if (signal in registeredPlugins[i]) {
					return true;
				}
			}

			return false;
		};

		/**
		 * Checks if the plugin exists in plugins
		 *
		 * @param  {string} plugin
		 * @return {boolean}
		 * @function
		 * @name exists
		 * @memberOf PluginManager.prototype
		 */
		base.exists = function (plugin) {
			if (plugin in plugins) {
				plugin = plugins[plugin];

				return typeof plugin === 'function' &&
					typeof plugin.prototype === 'object';
			}

			return false;
		};

		/**
		 * Checks if the passed plugin is currently registered.
		 *
		 * @param  {string} plugin
		 * @return {boolean}
		 * @function
		 * @name isRegistered
		 * @memberOf PluginManager.prototype
		 */
		base.isRegistered = function (plugin) {
			if (base.exists(plugin)) {
				var idx = registeredPlugins.length;

				while (idx--) {
					if (registeredPlugins[idx] instanceof plugins[plugin]) {
						return true;
					}
				}
			}

			return false;
		};

		/**
		 * Registers a plugin to receive signals
		 *
		 * @param  {string} plugin
		 * @return {boolean}
		 * @function
		 * @name register
		 * @memberOf PluginManager.prototype
		 */
		base.register = function (plugin) {
			if (!base.exists(plugin) || base.isRegistered(plugin)) {
				return false;
			}

			plugin = new plugins[plugin]();
			registeredPlugins.push(plugin);

			if ('init' in plugin) {
				plugin.init.call(thisObj);
			}

			return true;
		};

		/**
		 * Deregisters a plugin.
		 *
		 * @param  {string} plugin
		 * @return {boolean}
		 * @function
		 * @name deregister
		 * @memberOf PluginManager.prototype
		 */
		base.deregister = function (plugin) {
			var	removedPlugin,
				pluginIdx = registeredPlugins.length,
				removed   = false;

			if (!base.isRegistered(plugin)) {
				return removed;
			}

			while (pluginIdx--) {
				if (registeredPlugins[pluginIdx] instanceof plugins[plugin]) {
					removedPlugin = registeredPlugins.splice(pluginIdx, 1)[0];
					removed       = true;

					if ('destroy' in removedPlugin) {
						removedPlugin.destroy.call(thisObj);
					}
				}
			}

			return removed;
		};

		/**
		 * Clears all plugins and removes the owner reference.
		 *
		 * Calling any functions on this object after calling
		 * destroy will cause a JS error.
		 *
		 * @name destroy
		 * @memberOf PluginManager.prototype
		 */
		base.destroy = function () {
			var i = registeredPlugins.length;

			while (i--) {
				if ('destroy' in registeredPlugins[i]) {
					registeredPlugins[i].destroy.call(thisObj);
				}
			}

			registeredPlugins = [];
			thisObj    = null;
		};
	}
	PluginManager.plugins = plugins;

	/**
	 * Gets the text, start/end node and offset for
	 * length chars left or right of the passed node
	 * at the specified offset.
	 *
	 * @param  {Node}  node
	 * @param  {number}  offset
	 * @param  {boolean} isLeft
	 * @param  {number}  length
	 * @return {Object}
	 * @private
	 */
	var outerText = function (range, isLeft, length) {
		var nodeValue, remaining, start, end, node,
			text = '',
			next = range.startContainer,
			offset = range.startOffset;

		// Handle cases where node is a paragraph and offset
		// refers to the index of a text node.
		// 3 = text node
		if (next && next.nodeType !== 3) {
			next = next.childNodes[offset];
			offset = 0;
		}

		start = end = offset;

		while (length > text.length && next && next.nodeType === 3) {
			nodeValue = next.nodeValue;
			remaining = length - text.length;

			// If not the first node, start and end should be at their
			// max values as will be updated when getting the text
			if (node) {
				end = nodeValue.length;
				start = 0;
			}

			node = next;

			if (isLeft) {
				start = Math.max(end - remaining, 0);
				offset = start;

				text = nodeValue.substr(start, end - start) + text;
				next = node.previousSibling;
			} else {
				end = Math.min(remaining, nodeValue.length);
				offset = start + end;

				text += nodeValue.substr(start, end);
				next = node.nextSibling;
			}
		}

		return {
			node: node || next,
			offset: offset,
			text: text
		};
	};

	/**
	 * Range helper
	 *
	 * @class RangeHelper
	 * @name RangeHelper
	 */
	function RangeHelper(win, d, sanitize) {
		var	_createMarker, _prepareInput,
			doc          = d || win.contentDocument || win.document,
			startMarker  = 'sceditor-start-marker',
			endMarker    = 'sceditor-end-marker',
			base         = this;

		/**
		 * Inserts HTML into the current range replacing any selected
		 * text.
		 *
		 * If endHTML is specified the selected contents will be put between
		 * html and endHTML. If there is nothing selected html and endHTML are
		 * just concatenate together.
		 *
		 * @param {string} html
		 * @param {string} [endHTML]
		 * @return False on fail
		 * @function
		 * @name insertHTML
		 * @memberOf RangeHelper.prototype
		 */
		base.insertHTML = function (html, endHTML) {
			var	node, div,
				range = base.selectedRange();

			if (!range) {
				return false;
			}

			if (endHTML) {
				html += base.selectedHtml() + endHTML;
			}

			div           = createElement('p', {}, doc);
			node          = doc.createDocumentFragment();
			div.innerHTML = sanitize(html);

			while (div.firstChild) {
				appendChild(node, div.firstChild);
			}

			base.insertNode(node);
		};

		/**
		 * Prepares HTML to be inserted by adding a zero width space
		 * if the last child is empty and adding the range start/end
		 * markers to the last child.
		 *
		 * @param  {Node|string} node
		 * @param  {Node|string} [endNode]
		 * @param  {boolean} [returnHtml]
		 * @return {Node|string}
		 * @private
		 */
		_prepareInput = function (node, endNode, returnHtml) {
			var lastChild,
				frag = doc.createDocumentFragment();

			if (typeof node === 'string') {
				if (endNode) {
					node += base.selectedHtml() + endNode;
				}

				frag = parseHTML(node);
			} else {
				appendChild(frag, node);

				if (endNode) {
					appendChild(frag, base.selectedRange().extractContents());
					appendChild(frag, endNode);
				}
			}

			if (!(lastChild = frag.lastChild)) {
				return;
			}

			while (!isInline(lastChild.lastChild, true)) {
				lastChild = lastChild.lastChild;
			}

			if (canHaveChildren(lastChild)) {
				// Webkit won't allow the cursor to be placed inside an
				// empty tag, so add a zero width space to it.
				if (!lastChild.lastChild) {
					appendChild(lastChild, document.createTextNode('\u200B'));
				}
			} else {
				lastChild = frag;
			}

			base.removeMarkers();

			// Append marks to last child so when restored cursor will be in
			// the right place
			appendChild(lastChild, _createMarker(startMarker));
			appendChild(lastChild, _createMarker(endMarker));

			if (returnHtml) {
				var div = createElement('div');
				appendChild(div, frag);

				return div.innerHTML;
			}

			return frag;
		};

		/**
		 * The same as insertHTML except with DOM nodes instead
		 *
		 * <strong>Warning:</strong> the nodes must belong to the
		 * document they are being inserted into. Some browsers
		 * will throw exceptions if they don't.
		 *
		 * Returns boolean false on fail
		 *
		 * @param {Node} node
		 * @param {Node} endNode
		 * @return {false|undefined}
		 * @function
		 * @name insertNode
		 * @memberOf RangeHelper.prototype
		 */
		base.insertNode = function (node, endNode) {
			var	first, last,
				input  = _prepareInput(node, endNode),
				range  = base.selectedRange(),
				parent = range.commonAncestorContainer,
				emptyNodes = [];

			if (!input) {
				return false;
			}

			function removeIfEmpty(node) {
				// Only remove empty node if it wasn't already empty
				if (node && isEmpty(node) && emptyNodes.indexOf(node) < 0) {
					remove(node);
				}
			}

			if (range.startContainer !== range.endContainer) {
				each(parent.childNodes, function (_, node) {
					if (isEmpty(node)) {
						emptyNodes.push(node);
					}
				});

				first = input.firstChild;
				last = input.lastChild;
			}

			range.deleteContents();

			// FF allows <br /> to be selected but inserting a node
			// into <br /> will cause it not to be displayed so must
			// insert before the <br /> in FF.
			// 3 = TextNode
			if (parent && parent.nodeType !== 3 && !canHaveChildren(parent)) {
				insertBefore(input, parent);
			} else {
				range.insertNode(input);

				// If a node was split or its contents deleted, remove any resulting
				// empty tags. For example:
				// <p>|test</p><div>test|</div>
				// When deleteContents could become:
				// <p></p>|<div></div>
				// So remove the empty ones
				removeIfEmpty(first && first.previousSibling);
				removeIfEmpty(last && last.nextSibling);
			}

			base.restoreRange();
		};

		/**
		 * Clones the selected Range
		 *
		 * @return {Range}
		 * @function
		 * @name cloneSelected
		 * @memberOf RangeHelper.prototype
		 */
		base.cloneSelected = function () {
			var range = base.selectedRange();

			if (range) {
				return range.cloneRange();
			}
		};

		/**
		 * Gets the selected Range
		 *
		 * @return {Range}
		 * @function
		 * @name selectedRange
		 * @memberOf RangeHelper.prototype
		 */
		base.selectedRange = function () {
			var	range, firstChild,
				sel = win.getSelection();

			if (!sel) {
				return;
			}

			// When creating a new range, set the start to the first child
			// element of the body element to avoid errors in FF.
			if (sel.rangeCount <= 0) {
				firstChild = doc.body;
				while (firstChild.firstChild) {
					firstChild = firstChild.firstChild;
				}

				range = doc.createRange();
				// Must be setStartBefore otherwise it can cause infinite
				// loops with lists in WebKit. See issue 442
				range.setStartBefore(firstChild);

				sel.addRange(range);
			}

			if (sel.rangeCount > 0) {
				range = sel.getRangeAt(0);
			}

			return range;
		};

		/**
		 * Gets if there is currently a selection
		 *
		 * @return {boolean}
		 * @function
		 * @name hasSelection
		 * @since 1.4.4
		 * @memberOf RangeHelper.prototype
		 */
		base.hasSelection = function () {
			var	sel = win.getSelection();

			return sel && sel.rangeCount > 0;
		};

		/**
		 * Gets the currently selected HTML
		 *
		 * @return {string}
		 * @function
		 * @name selectedHtml
		 * @memberOf RangeHelper.prototype
		 */
		base.selectedHtml = function () {
			var	div,
				range = base.selectedRange();

			if (range) {
				div = createElement('p', {}, doc);
				appendChild(div, range.cloneContents());

				return div.innerHTML;
			}

			return '';
		};

		/**
		 * Gets the parent node of the selected contents in the range
		 *
		 * @return {HTMLElement}
		 * @function
		 * @name parentNode
		 * @memberOf RangeHelper.prototype
		 */
		base.parentNode = function () {
			var range = base.selectedRange();

			if (range) {
				return range.commonAncestorContainer;
			}
		};

		/**
		 * Gets the first block level parent of the selected
		 * contents of the range.
		 *
		 * @return {HTMLElement}
		 * @function
		 * @name getFirstBlockParent
		 * @memberOf RangeHelper.prototype
		 */
		/**
		 * Gets the first block level parent of the selected
		 * contents of the range.
		 *
		 * @param {Node} [n] The element to get the first block level parent from
		 * @return {HTMLElement}
		 * @function
		 * @name getFirstBlockParent^2
		 * @since 1.4.1
		 * @memberOf RangeHelper.prototype
		 */
		base.getFirstBlockParent = function (node) {
			var func = function (elm) {
				if (!isInline(elm, true)) {
					return elm;
				}

				elm = elm ? elm.parentNode : null;

				return elm ? func(elm) : elm;
			};

			return func(node || base.parentNode());
		};

		/**
		 * Inserts a node at either the start or end of the current selection
		 *
		 * @param {Bool} start
		 * @param {Node} node
		 * @function
		 * @name insertNodeAt
		 * @memberOf RangeHelper.prototype
		 */
		base.insertNodeAt = function (start, node) {
			var	currentRange = base.selectedRange(),
				range        = base.cloneSelected();

			if (!range) {
				return false;
			}

			range.collapse(start);
			range.insertNode(node);

			// Reselect the current range.
			// Fixes issue with Chrome losing the selection. Issue#82
			base.selectRange(currentRange);
		};

		/**
		 * Creates a marker node
		 *
		 * @param {string} id
		 * @return {HTMLSpanElement}
		 * @private
		 */
		_createMarker = function (id) {
			base.removeMarker(id);

			var marker  = createElement('span', {
				id: id,
				className: 'sceditor-selection sceditor-ignore',
				style: 'display:none;line-height:0'
			}, doc);

			marker.innerHTML = ' ';

			return marker;
		};

		/**
		 * Inserts start/end markers for the current selection
		 * which can be used by restoreRange to re-select the
		 * range.
		 *
		 * @memberOf RangeHelper.prototype
		 * @function
		 * @name insertMarkers
		 */
		base.insertMarkers = function () {
			var	currentRange = base.selectedRange();
			var startNode = _createMarker(startMarker);

			base.removeMarkers();
			base.insertNodeAt(true, startNode);

			// Fixes issue with end marker sometimes being placed before
			// the start marker when the range is collapsed.
			if (currentRange && currentRange.collapsed) {
				startNode.parentNode.insertBefore(
					_createMarker(endMarker), startNode.nextSibling);
			} else {
				base.insertNodeAt(false, _createMarker(endMarker));
			}
		};

		/**
		 * Gets the marker with the specified ID
		 *
		 * @param {string} id
		 * @return {Node}
		 * @function
		 * @name getMarker
		 * @memberOf RangeHelper.prototype
		 */
		base.getMarker = function (id) {
			return doc.getElementById(id);
		};

		/**
		 * Removes the marker with the specified ID
		 *
		 * @param {string} id
		 * @function
		 * @name removeMarker
		 * @memberOf RangeHelper.prototype
		 */
		base.removeMarker = function (id) {
			var marker = base.getMarker(id);

			if (marker) {
				remove(marker);
			}
		};

		/**
		 * Removes the start/end markers
		 *
		 * @function
		 * @name removeMarkers
		 * @memberOf RangeHelper.prototype
		 */
		base.removeMarkers = function () {
			base.removeMarker(startMarker);
			base.removeMarker(endMarker);
		};

		/**
		 * Saves the current range location. Alias of insertMarkers()
		 *
		 * @function
		 * @name saveRage
		 * @memberOf RangeHelper.prototype
		 */
		base.saveRange = function () {
			base.insertMarkers();
		};

		/**
		 * Select the specified range
		 *
		 * @param {Range} range
		 * @function
		 * @name selectRange
		 * @memberOf RangeHelper.prototype
		 */
		base.selectRange = function (range) {
			var lastChild;
			var sel = win.getSelection();
			var container = range.endContainer;

			// Check if cursor is set after a BR when the BR is the only
			// child of the parent. In Firefox this causes a line break
			// to occur when something is typed. See issue #321
			if (range.collapsed && container &&
				!isInline(container, true)) {

				lastChild = container.lastChild;
				while (lastChild && is(lastChild, '.sceditor-ignore')) {
					lastChild = lastChild.previousSibling;
				}

				if (is(lastChild, 'br')) {
					var rng = doc.createRange();
					rng.setEndAfter(lastChild);
					rng.collapse(false);

					if (base.compare(range, rng)) {
						range.setStartBefore(lastChild);
						range.collapse(true);
					}
				}
			}

			if (sel) {
				base.clear();
				sel.addRange(range);
			}
		};

		/**
		 * Restores the last range saved by saveRange() or insertMarkers()
		 *
		 * @function
		 * @name restoreRange
		 * @memberOf RangeHelper.prototype
		 */
		base.restoreRange = function () {
			var	isCollapsed,
				range = base.selectedRange(),
				start = base.getMarker(startMarker),
				end   = base.getMarker(endMarker);

			if (!start || !end || !range) {
				return false;
			}

			isCollapsed = start.nextSibling === end;

			range = doc.createRange();
			range.setStartBefore(start);
			range.setEndAfter(end);

			if (isCollapsed) {
				range.collapse(true);
			}

			base.selectRange(range);
			base.removeMarkers();
		};

		/**
		 * Selects the text left and right of the current selection
		 *
		 * @param {number} left
		 * @param {number} right
		 * @since 1.4.3
		 * @function
		 * @name selectOuterText
		 * @memberOf RangeHelper.prototype
		 */
		base.selectOuterText = function (left, right) {
			var start, end,
				range = base.cloneSelected();

			if (!range) {
				return false;
			}

			range.collapse(false);

			start = outerText(range, true, left);
			end = outerText(range, false, right);

			range.setStart(start.node, start.offset);
			range.setEnd(end.node, end.offset);

			base.selectRange(range);
		};

		/**
		 * Gets the text left or right of the current selection
		 *
		 * @param {boolean} before
		 * @param {number} length
		 * @return {string}
		 * @since 1.4.3
		 * @function
		 * @name selectOuterText
		 * @memberOf RangeHelper.prototype
		 */
		base.getOuterText = function (before, length) {
			var	range = base.cloneSelected();

			if (!range) {
				return '';
			}

			range.collapse(!before);

			return outerText(range, before, length).text;
		};

		/**
		 * Replaces keywords with values based on the current caret position
		 *
		 * @param {Array}   keywords
		 * @param {boolean} includeAfter      If to include the text after the
		 *                                    current caret position or just
		 *                                    text before
		 * @param {boolean} keywordsSorted    If the keywords array is pre
		 *                                    sorted shortest to longest
		 * @param {number}  longestKeyword    Length of the longest keyword
		 * @param {boolean} requireWhitespace If the key must be surrounded
		 *                                    by whitespace
		 * @param {string}  keypressChar      If this is being called from
		 *                                    a keypress event, this should be
		 *                                    set to the pressed character
		 * @return {boolean}
		 * @function
		 * @name replaceKeyword
		 * @memberOf RangeHelper.prototype
		 */
		// eslint-disable-next-line max-params
		base.replaceKeyword = function (
			keywords,
			includeAfter,
			keywordsSorted,
			longestKeyword,
			requireWhitespace,
			keypressChar
		) {
			if (!keywordsSorted) {
				keywords.sort(function (a, b) {
					return a[0].length - b[0].length;
				});
			}

			var outerText, match, matchPos, startIndex,
				leftLen, charsLeft, keyword, keywordLen,
				whitespaceRegex = '(^|[\\s\xA0\u2002\u2003\u2009])',
				keywordIdx      = keywords.length,
				whitespaceLen   = requireWhitespace ? 1 : 0,
				maxKeyLen       = longestKeyword ||
					keywords[keywordIdx - 1][0].length;

			if (requireWhitespace) {
				maxKeyLen++;
			}

			keypressChar = keypressChar || '';
			outerText    = base.getOuterText(true, maxKeyLen);
			leftLen      = outerText.length;
			outerText   += keypressChar;

			if (includeAfter) {
				outerText += base.getOuterText(false, maxKeyLen);
			}

			while (keywordIdx--) {
				keyword    = keywords[keywordIdx][0];
				keywordLen = keyword.length;
				startIndex = Math.max(0, leftLen - keywordLen - whitespaceLen);
				matchPos   = -1;

				if (requireWhitespace) {
					match = outerText
						.substr(startIndex)
						.match(new RegExp(whitespaceRegex +
							regex(keyword) + whitespaceRegex));

					if (match) {
						// Add the length of the text that was removed by
						// substr() and also add 1 for the whitespace
						matchPos = match.index + startIndex + match[1].length;
					}
				} else {
					matchPos = outerText.indexOf(keyword, startIndex);
				}

				if (matchPos > -1) {
					// Make sure the match is between before and
					// after, not just entirely in one side or the other
					if (matchPos <= leftLen &&
						matchPos + keywordLen + whitespaceLen >= leftLen) {
						charsLeft = leftLen - matchPos;

						// If the keypress char is white space then it should
						// not be replaced, only chars that are part of the
						// key should be replaced.
						base.selectOuterText(
							charsLeft,
							keywordLen - charsLeft -
								(/^\S/.test(keypressChar) ? 1 : 0)
						);

						base.insertHTML(keywords[keywordIdx][1]);
						return true;
					}
				}
			}

			return false;
		};

		/**
		 * Compares two ranges.
		 *
		 * If rangeB is undefined it will be set to
		 * the current selected range
		 *
		 * @param  {Range} rngA
		 * @param  {Range} [rngB]
		 * @return {boolean}
		 * @function
		 * @name compare
		 * @memberOf RangeHelper.prototype
		 */
		base.compare = function (rngA, rngB) {
			if (!rngB) {
				rngB = base.selectedRange();
			}

			if (!rngA || !rngB) {
				return !rngA && !rngB;
			}

			return rngA.compareBoundaryPoints(Range.END_TO_END, rngB) === 0 &&
				rngA.compareBoundaryPoints(Range.START_TO_START, rngB) === 0;
		};

		/**
		 * Removes any current selection
		 *
		 * @since 1.4.6
		 * @function
		 * @name clear
		 * @memberOf RangeHelper.prototype
		 */
		base.clear = function () {
			var sel = win.getSelection();

			if (sel) {
				if (sel.removeAllRanges) {
					sel.removeAllRanges();
				} else if (sel.empty) {
					sel.empty();
				}
			}
		};
	}

	var USER_AGENT = navigator.userAgent;

	/**
	 * Detects if the browser is iOS
	 *
	 * Needed to fix iOS specific bugs
	 *
	 * @function
	 * @name ios
	 * @memberOf jQuery.sceditor
	 * @type {boolean}
	 */
	var ios = /iPhone|iPod|iPad| wosbrowser\//i.test(USER_AGENT);

	/**
	 * If the browser supports WYSIWYG editing (e.g. older mobile browsers).
	 *
	 * @function
	 * @name isWysiwygSupported
	 * @return {boolean}
	 */
	var isWysiwygSupported = (function () {
		var	match, isUnsupported;

		// IE is the only browser to support documentMode
		var ie = !!window.document.documentMode;
		var legacyEdge = '-ms-ime-align' in document.documentElement.style;

		var div = document.createElement('div');
		div.contentEditable = true;

		// Check if the contentEditable attribute is supported
		if (!('contentEditable' in document.documentElement) ||
			div.contentEditable !== 'true') {
			return false;
		}

		// I think blackberry supports contentEditable or will at least
		// give a valid value for the contentEditable detection above
		// so it isn't included in the below tests.

		// I hate having to do UA sniffing but some mobile browsers say they
		// support contentediable when it isn't usable, i.e. you can't enter
		// text.
		// This is the only way I can think of to detect them which is also how
		// every other editor I've seen deals with this issue.

		// Exclude Opera mobile and mini
		isUnsupported = /Opera Mobi|Opera Mini/i.test(USER_AGENT);

		if (/Android/i.test(USER_AGENT)) {
			isUnsupported = true;

			if (/Safari/.test(USER_AGENT)) {
				// Android browser 534+ supports content editable
				// This also matches Chrome which supports content editable too
				match = /Safari\/(\d+)/.exec(USER_AGENT);
				isUnsupported = (!match || !match[1] ? true : match[1] < 534);
			}
		}

		// The current version of Amazon Silk supports it, older versions didn't
		// As it uses webkit like Android, assume it's the same and started
		// working at versions >= 534
		if (/ Silk\//i.test(USER_AGENT)) {
			match = /AppleWebKit\/(\d+)/.exec(USER_AGENT);
			isUnsupported = (!match || !match[1] ? true : match[1] < 534);
		}

		// iOS 5+ supports content editable
		if (ios) {
			// Block any version <= 4_x(_x)
			isUnsupported = /OS [0-4](_\d)+ like Mac/i.test(USER_AGENT);
		}

		// Firefox does support WYSIWYG on mobiles so override
		// any previous value if using FF
		if (/Firefox/i.test(USER_AGENT)) {
			isUnsupported = false;
		}

		if (/OneBrowser/i.test(USER_AGENT)) {
			isUnsupported = false;
		}

		// UCBrowser works but doesn't give a unique user agent
		if (navigator.vendor === 'UCWEB') {
			isUnsupported = false;
		}

		// IE and legacy edge are not supported any more
		if (ie || legacyEdge) {
			isUnsupported = true;
		}

		return !isUnsupported;
	}());

	/**
	 * Checks all emoticons are surrounded by whitespace and
	 * replaces any that aren't with with their emoticon code.
	 *
	 * @param {HTMLElement} node
	 * @param {rangeHelper} rangeHelper
	 * @return {void}
	 */
	function checkWhitespace(node, rangeHelper) {
		var noneWsRegex = /[^\s\xA0\u2002\u2003\u2009]+/;
		var emoticons = node && find(node, 'img[data-sceditor-emoticon]');

		if (!node || !emoticons.length) {
			return;
		}

		for (var i = 0; i < emoticons.length; i++) {
			var emoticon = emoticons[i];
			var parent = emoticon.parentNode;
			var prev = emoticon.previousSibling;
			var next = emoticon.nextSibling;

			if ((!prev || !noneWsRegex.test(prev.nodeValue.slice(-1))) &&
				(!next || !noneWsRegex.test((next.nodeValue || '')[0]))) {
				continue;
			}

			var range = rangeHelper.cloneSelected();
			var rangeStart = -1;
			var rangeStartContainer = range.startContainer;
			var previousText = prev.nodeValue || '';

			previousText += data(emoticon, 'sceditor-emoticon');

			// If the cursor is after the removed emoticon, add
			// the length of the newly added text to it
			if (rangeStartContainer === next) {
				rangeStart = previousText.length + range.startOffset;
			}

			// If the cursor is set before the next node, set it to
			// the end of the new text node
			if (rangeStartContainer === node &&
				node.childNodes[range.startOffset] === next) {
				rangeStart = previousText.length;
			}

			// If the cursor is set before the removed emoticon,
			// just keep it at that position
			if (rangeStartContainer === prev) {
				rangeStart = range.startOffset;
			}

			if (!next || next.nodeType !== TEXT_NODE) {
				next = parent.insertBefore(
					parent.ownerDocument.createTextNode(''), next
				);
			}

			next.insertData(0, previousText);
			remove(prev);
			remove(emoticon);

			// Need to update the range starting position if it's been modified
			if (rangeStart > -1) {
				range.setStart(next, rangeStart);
				range.collapse(true);
				rangeHelper.selectRange(range);
			}
		}
	}
	/**
	 * Replaces any emoticons inside the root node with images.
	 *
	 * emoticons should be an object where the key is the emoticon
	 * code and the value is the HTML to replace it with.
	 *
	 * @param {HTMLElement} root
	 * @param {Object<string, string>} emoticons
	 * @param {boolean} emoticonsCompat
	 * @return {void}
	 */
	function replace(root, emoticons, emoticonsCompat) {
		var	doc           = root.ownerDocument;
		var space         = '(^|\\s|\xA0|\u2002|\u2003|\u2009|$)';
		var emoticonCodes = [];
		var emoticonRegex = {};

		// TODO: Make this tag configurable.
		if (parent(root, 'code')) {
			return;
		}

		each(emoticons, function (key) {
			emoticonRegex[key] = new RegExp(space + regex(key) + space);
			emoticonCodes.push(key);
		});

		// Sort keys longest to shortest so that longer keys
		// take precedence (avoids bugs with shorter keys partially
		// matching longer ones)
		emoticonCodes.sort(function (a, b) {
			return b.length - a.length;
		});

		(function convert(node) {
			node = node.firstChild;

			while (node) {
				// TODO: Make this tag configurable.
				if (node.nodeType === ELEMENT_NODE && !is(node, 'code')) {
					convert(node);
				}

				if (node.nodeType === TEXT_NODE) {
					for (var i = 0; i < emoticonCodes.length; i++) {
						var text  = node.nodeValue;
						var key   = emoticonCodes[i];
						var index = emoticonsCompat ?
							text.search(emoticonRegex[key]) :
							text.indexOf(key);

						if (index > -1) {
							// When emoticonsCompat is enabled this will be the
							// position after any white space
							var startIndex = text.indexOf(key, index);
							var fragment   = parseHTML(emoticons[key], doc);
							var after      = text.substr(startIndex + key.length);

							fragment.appendChild(doc.createTextNode(after));

							node.nodeValue = text.substr(0, startIndex);
							node.parentNode
								.insertBefore(fragment, node.nextSibling);
						}
					}
				}

				node = node.nextSibling;
			}
		}(root));
	}

	/*! @license DOMPurify | (c) Cure53 and other contributors | Released under the Apache license 2.0 and Mozilla Public License 2.0 | github.com/cure53/DOMPurify/blob/2.2.2/LICENSE */

	function _toConsumableArray(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

	var hasOwnProperty = Object.hasOwnProperty,
	    setPrototypeOf = Object.setPrototypeOf,
	    isFrozen = Object.isFrozen,
	    getPrototypeOf = Object.getPrototypeOf,
	    getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
	var freeze = Object.freeze,
	    seal = Object.seal,
	    create = Object.create; // eslint-disable-line import/no-mutable-exports

	var _ref = typeof Reflect !== 'undefined' && Reflect,
	    apply = _ref.apply,
	    construct = _ref.construct;

	if (!apply) {
	  apply = function apply(fun, thisValue, args) {
	    return fun.apply(thisValue, args);
	  };
	}

	if (!freeze) {
	  freeze = function freeze(x) {
	    return x;
	  };
	}

	if (!seal) {
	  seal = function seal(x) {
	    return x;
	  };
	}

	if (!construct) {
	  construct = function construct(Func, args) {
	    return new (Function.prototype.bind.apply(Func, [null].concat(_toConsumableArray(args))))();
	  };
	}

	var arrayForEach = unapply(Array.prototype.forEach);
	var arrayPop = unapply(Array.prototype.pop);
	var arrayPush = unapply(Array.prototype.push);

	var stringToLowerCase = unapply(String.prototype.toLowerCase);
	var stringMatch = unapply(String.prototype.match);
	var stringReplace = unapply(String.prototype.replace);
	var stringIndexOf = unapply(String.prototype.indexOf);
	var stringTrim = unapply(String.prototype.trim);

	var regExpTest = unapply(RegExp.prototype.test);

	var typeErrorCreate = unconstruct(TypeError);

	function unapply(func) {
	  return function (thisArg) {
	    for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	      args[_key - 1] = arguments[_key];
	    }

	    return apply(func, thisArg, args);
	  };
	}

	function unconstruct(func) {
	  return function () {
	    for (var _len2 = arguments.length, args = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
	      args[_key2] = arguments[_key2];
	    }

	    return construct(func, args);
	  };
	}

	/* Add properties to a lookup table */
	function addToSet(set, array) {
	  if (setPrototypeOf) {
	    // Make 'in' and truthy checks like Boolean(set.constructor)
	    // independent of any properties defined on Object.prototype.
	    // Prevent prototype setters from intercepting set as a this value.
	    setPrototypeOf(set, null);
	  }

	  var l = array.length;
	  while (l--) {
	    var element = array[l];
	    if (typeof element === 'string') {
	      var lcElement = stringToLowerCase(element);
	      if (lcElement !== element) {
	        // Config presets (e.g. tags.js, attrs.js) are immutable.
	        if (!isFrozen(array)) {
	          array[l] = lcElement;
	        }

	        element = lcElement;
	      }
	    }

	    set[element] = true;
	  }

	  return set;
	}

	/* Shallow clone an object */
	function clone(object) {
	  var newObject = create(null);

	  var property = void 0;
	  for (property in object) {
	    if (apply(hasOwnProperty, object, [property])) {
	      newObject[property] = object[property];
	    }
	  }

	  return newObject;
	}

	/* IE10 doesn't support __lookupGetter__ so lets'
	 * simulate it. It also automatically checks
	 * if the prop is function or getter and behaves
	 * accordingly. */
	function lookupGetter(object, prop) {
	  while (object !== null) {
	    var desc = getOwnPropertyDescriptor(object, prop);
	    if (desc) {
	      if (desc.get) {
	        return unapply(desc.get);
	      }

	      if (typeof desc.value === 'function') {
	        return unapply(desc.value);
	      }
	    }

	    object = getPrototypeOf(object);
	  }

	  return null;
	}

	var html = freeze(['a', 'abbr', 'acronym', 'address', 'area', 'article', 'aside', 'audio', 'b', 'bdi', 'bdo', 'big', 'blink', 'blockquote', 'body', 'br', 'button', 'canvas', 'caption', 'center', 'cite', 'code', 'col', 'colgroup', 'content', 'data', 'datalist', 'dd', 'decorator', 'del', 'details', 'dfn', 'dialog', 'dir', 'div', 'dl', 'dt', 'element', 'em', 'fieldset', 'figcaption', 'figure', 'font', 'footer', 'form', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'head', 'header', 'hgroup', 'hr', 'html', 'i', 'img', 'input', 'ins', 'kbd', 'label', 'legend', 'li', 'main', 'map', 'mark', 'marquee', 'menu', 'menuitem', 'meter', 'nav', 'nobr', 'ol', 'optgroup', 'option', 'output', 'p', 'picture', 'pre', 'progress', 'q', 'rp', 'rt', 'ruby', 's', 'samp', 'section', 'select', 'shadow', 'small', 'source', 'spacer', 'span', 'strike', 'strong', 'style', 'sub', 'summary', 'sup', 'table', 'tbody', 'td', 'template', 'textarea', 'tfoot', 'th', 'thead', 'time', 'tr', 'track', 'tt', 'u', 'ul', 'var', 'video', 'wbr']);

	// SVG
	var svg = freeze(['svg', 'a', 'altglyph', 'altglyphdef', 'altglyphitem', 'animatecolor', 'animatemotion', 'animatetransform', 'circle', 'clippath', 'defs', 'desc', 'ellipse', 'filter', 'font', 'g', 'glyph', 'glyphref', 'hkern', 'image', 'line', 'lineargradient', 'marker', 'mask', 'metadata', 'mpath', 'path', 'pattern', 'polygon', 'polyline', 'radialgradient', 'rect', 'stop', 'style', 'switch', 'symbol', 'text', 'textpath', 'title', 'tref', 'tspan', 'view', 'vkern']);

	var svgFilters = freeze(['feBlend', 'feColorMatrix', 'feComponentTransfer', 'feComposite', 'feConvolveMatrix', 'feDiffuseLighting', 'feDisplacementMap', 'feDistantLight', 'feFlood', 'feFuncA', 'feFuncB', 'feFuncG', 'feFuncR', 'feGaussianBlur', 'feMerge', 'feMergeNode', 'feMorphology', 'feOffset', 'fePointLight', 'feSpecularLighting', 'feSpotLight', 'feTile', 'feTurbulence']);

	// List of SVG elements that are disallowed by default.
	// We still need to know them so that we can do namespace
	// checks properly in case one wants to add them to
	// allow-list.
	var svgDisallowed = freeze(['animate', 'color-profile', 'cursor', 'discard', 'fedropshadow', 'feimage', 'font-face', 'font-face-format', 'font-face-name', 'font-face-src', 'font-face-uri', 'foreignobject', 'hatch', 'hatchpath', 'mesh', 'meshgradient', 'meshpatch', 'meshrow', 'missing-glyph', 'script', 'set', 'solidcolor', 'unknown', 'use']);

	var mathMl = freeze(['math', 'menclose', 'merror', 'mfenced', 'mfrac', 'mglyph', 'mi', 'mlabeledtr', 'mmultiscripts', 'mn', 'mo', 'mover', 'mpadded', 'mphantom', 'mroot', 'mrow', 'ms', 'mspace', 'msqrt', 'mstyle', 'msub', 'msup', 'msubsup', 'mtable', 'mtd', 'mtext', 'mtr', 'munder', 'munderover']);

	// Similarly to SVG, we want to know all MathML elements,
	// even those that we disallow by default.
	var mathMlDisallowed = freeze(['maction', 'maligngroup', 'malignmark', 'mlongdiv', 'mscarries', 'mscarry', 'msgroup', 'mstack', 'msline', 'msrow', 'semantics', 'annotation', 'annotation-xml', 'mprescripts', 'none']);

	var text = freeze(['#text']);

	var html$1 = freeze(['accept', 'action', 'align', 'alt', 'autocapitalize', 'autocomplete', 'autopictureinpicture', 'autoplay', 'background', 'bgcolor', 'border', 'capture', 'cellpadding', 'cellspacing', 'checked', 'cite', 'class', 'clear', 'color', 'cols', 'colspan', 'controls', 'controlslist', 'coords', 'crossorigin', 'datetime', 'decoding', 'default', 'dir', 'disabled', 'disablepictureinpicture', 'disableremoteplayback', 'download', 'draggable', 'enctype', 'enterkeyhint', 'face', 'for', 'headers', 'height', 'hidden', 'high', 'href', 'hreflang', 'id', 'inputmode', 'integrity', 'ismap', 'kind', 'label', 'lang', 'list', 'loading', 'loop', 'low', 'max', 'maxlength', 'media', 'method', 'min', 'minlength', 'multiple', 'muted', 'name', 'noshade', 'novalidate', 'nowrap', 'open', 'optimum', 'pattern', 'placeholder', 'playsinline', 'poster', 'preload', 'pubdate', 'radiogroup', 'readonly', 'rel', 'required', 'rev', 'reversed', 'role', 'rows', 'rowspan', 'spellcheck', 'scope', 'selected', 'shape', 'size', 'sizes', 'span', 'srclang', 'start', 'src', 'srcset', 'step', 'style', 'summary', 'tabindex', 'title', 'translate', 'type', 'usemap', 'valign', 'value', 'width', 'xmlns']);

	var svg$1 = freeze(['accent-height', 'accumulate', 'additive', 'alignment-baseline', 'ascent', 'attributename', 'attributetype', 'azimuth', 'basefrequency', 'baseline-shift', 'begin', 'bias', 'by', 'class', 'clip', 'clippathunits', 'clip-path', 'clip-rule', 'color', 'color-interpolation', 'color-interpolation-filters', 'color-profile', 'color-rendering', 'cx', 'cy', 'd', 'dx', 'dy', 'diffuseconstant', 'direction', 'display', 'divisor', 'dur', 'edgemode', 'elevation', 'end', 'fill', 'fill-opacity', 'fill-rule', 'filter', 'filterunits', 'flood-color', 'flood-opacity', 'font-family', 'font-size', 'font-size-adjust', 'font-stretch', 'font-style', 'font-variant', 'font-weight', 'fx', 'fy', 'g1', 'g2', 'glyph-name', 'glyphref', 'gradientunits', 'gradienttransform', 'height', 'href', 'id', 'image-rendering', 'in', 'in2', 'k', 'k1', 'k2', 'k3', 'k4', 'kerning', 'keypoints', 'keysplines', 'keytimes', 'lang', 'lengthadjust', 'letter-spacing', 'kernelmatrix', 'kernelunitlength', 'lighting-color', 'local', 'marker-end', 'marker-mid', 'marker-start', 'markerheight', 'markerunits', 'markerwidth', 'maskcontentunits', 'maskunits', 'max', 'mask', 'media', 'method', 'mode', 'min', 'name', 'numoctaves', 'offset', 'operator', 'opacity', 'order', 'orient', 'orientation', 'origin', 'overflow', 'paint-order', 'path', 'pathlength', 'patterncontentunits', 'patterntransform', 'patternunits', 'points', 'preservealpha', 'preserveaspectratio', 'primitiveunits', 'r', 'rx', 'ry', 'radius', 'refx', 'refy', 'repeatcount', 'repeatdur', 'restart', 'result', 'rotate', 'scale', 'seed', 'shape-rendering', 'specularconstant', 'specularexponent', 'spreadmethod', 'startoffset', 'stddeviation', 'stitchtiles', 'stop-color', 'stop-opacity', 'stroke-dasharray', 'stroke-dashoffset', 'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-opacity', 'stroke', 'stroke-width', 'style', 'surfacescale', 'systemlanguage', 'tabindex', 'targetx', 'targety', 'transform', 'text-anchor', 'text-decoration', 'text-rendering', 'textlength', 'type', 'u1', 'u2', 'unicode', 'values', 'viewbox', 'visibility', 'version', 'vert-adv-y', 'vert-origin-x', 'vert-origin-y', 'width', 'word-spacing', 'wrap', 'writing-mode', 'xchannelselector', 'ychannelselector', 'x', 'x1', 'x2', 'xmlns', 'y', 'y1', 'y2', 'z', 'zoomandpan']);

	var mathMl$1 = freeze(['accent', 'accentunder', 'align', 'bevelled', 'close', 'columnsalign', 'columnlines', 'columnspan', 'denomalign', 'depth', 'dir', 'display', 'displaystyle', 'encoding', 'fence', 'frame', 'height', 'href', 'id', 'largeop', 'length', 'linethickness', 'lspace', 'lquote', 'mathbackground', 'mathcolor', 'mathsize', 'mathvariant', 'maxsize', 'minsize', 'movablelimits', 'notation', 'numalign', 'open', 'rowalign', 'rowlines', 'rowspacing', 'rowspan', 'rspace', 'rquote', 'scriptlevel', 'scriptminsize', 'scriptsizemultiplier', 'selection', 'separator', 'separators', 'stretchy', 'subscriptshift', 'supscriptshift', 'symmetric', 'voffset', 'width', 'xmlns']);

	var xml = freeze(['xlink:href', 'xml:id', 'xlink:title', 'xml:space', 'xmlns:xlink']);

	// eslint-disable-next-line unicorn/better-regex
	var MUSTACHE_EXPR = seal(/\{\{[\s\S]*|[\s\S]*\}\}/gm); // Specify template detection regex for SAFE_FOR_TEMPLATES mode
	var ERB_EXPR = seal(/<%[\s\S]*|[\s\S]*%>/gm);
	var DATA_ATTR = seal(/^data-[\-\w.\u00B7-\uFFFF]/); // eslint-disable-line no-useless-escape
	var ARIA_ATTR = seal(/^aria-[\-\w]+$/); // eslint-disable-line no-useless-escape
	var IS_ALLOWED_URI = seal(/^(?:(?:(?:f|ht)tps?|mailto|tel|callto|cid|xmpp):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i // eslint-disable-line no-useless-escape
	);
	var IS_SCRIPT_OR_DATA = seal(/^(?:\w+script|data):/i);
	var ATTR_WHITESPACE = seal(/[\u0000-\u0020\u00A0\u1680\u180E\u2000-\u2029\u205F\u3000]/g // eslint-disable-line no-control-regex
	);

	var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

	function _toConsumableArray$1(arr) { if (Array.isArray(arr)) { for (var i = 0, arr2 = Array(arr.length); i < arr.length; i++) { arr2[i] = arr[i]; } return arr2; } else { return Array.from(arr); } }

	var getGlobal = function getGlobal() {
	  return typeof window === 'undefined' ? null : window;
	};

	/**
	 * Creates a no-op policy for internal use only.
	 * Don't export this function outside this module!
	 * @param {?TrustedTypePolicyFactory} trustedTypes The policy factory.
	 * @param {Document} document The document object (to determine policy name suffix)
	 * @return {?TrustedTypePolicy} The policy created (or null, if Trusted Types
	 * are not supported).
	 */
	var _createTrustedTypesPolicy = function _createTrustedTypesPolicy(trustedTypes, document) {
	  if ((typeof trustedTypes === 'undefined' ? 'undefined' : _typeof(trustedTypes)) !== 'object' || typeof trustedTypes.createPolicy !== 'function') {
	    return null;
	  }

	  // Allow the callers to control the unique policy name
	  // by adding a data-tt-policy-suffix to the script element with the DOMPurify.
	  // Policy creation with duplicate names throws in Trusted Types.
	  var suffix = null;
	  var ATTR_NAME = 'data-tt-policy-suffix';
	  if (document.currentScript && document.currentScript.hasAttribute(ATTR_NAME)) {
	    suffix = document.currentScript.getAttribute(ATTR_NAME);
	  }

	  var policyName = 'dompurify' + (suffix ? '#' + suffix : '');

	  try {
	    return trustedTypes.createPolicy(policyName, {
	      createHTML: function createHTML(html$$1) {
	        return html$$1;
	      }
	    });
	  } catch (_) {
	    // Policy creation failed (most likely another DOMPurify script has
	    // already run). Skip creating the policy, as this will only cause errors
	    // if TT are enforced.
	    console.warn('TrustedTypes policy ' + policyName + ' could not be created.');
	    return null;
	  }
	};

	function createDOMPurify() {
	  var window = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : getGlobal();

	  var DOMPurify = function DOMPurify(root) {
	    return createDOMPurify(root);
	  };

	  /**
	   * Version label, exposed for easier checks
	   * if DOMPurify is up to date or not
	   */
	  DOMPurify.version = '2.2.6';

	  /**
	   * Array of elements that DOMPurify removed during sanitation.
	   * Empty if nothing was removed.
	   */
	  DOMPurify.removed = [];

	  if (!window || !window.document || window.document.nodeType !== 9) {
	    // Not running in a browser, provide a factory function
	    // so that you can pass your own Window
	    DOMPurify.isSupported = false;

	    return DOMPurify;
	  }

	  var originalDocument = window.document;

	  var document = window.document;
	  var DocumentFragment = window.DocumentFragment,
	      HTMLTemplateElement = window.HTMLTemplateElement,
	      Node = window.Node,
	      Element = window.Element,
	      NodeFilter = window.NodeFilter,
	      _window$NamedNodeMap = window.NamedNodeMap,
	      NamedNodeMap = _window$NamedNodeMap === undefined ? window.NamedNodeMap || window.MozNamedAttrMap : _window$NamedNodeMap,
	      Text = window.Text,
	      Comment = window.Comment,
	      DOMParser = window.DOMParser,
	      trustedTypes = window.trustedTypes;


	  var ElementPrototype = Element.prototype;

	  var cloneNode = lookupGetter(ElementPrototype, 'cloneNode');
	  var getNextSibling = lookupGetter(ElementPrototype, 'nextSibling');
	  var getChildNodes = lookupGetter(ElementPrototype, 'childNodes');
	  var getParentNode = lookupGetter(ElementPrototype, 'parentNode');

	  // As per issue #47, the web-components registry is inherited by a
	  // new document created via createHTMLDocument. As per the spec
	  // (http://w3c.github.io/webcomponents/spec/custom/#creating-and-passing-registries)
	  // a new empty registry is used when creating a template contents owner
	  // document, so we use that as our parent document to ensure nothing
	  // is inherited.
	  if (typeof HTMLTemplateElement === 'function') {
	    var template = document.createElement('template');
	    if (template.content && template.content.ownerDocument) {
	      document = template.content.ownerDocument;
	    }
	  }

	  var trustedTypesPolicy = _createTrustedTypesPolicy(trustedTypes, originalDocument);
	  var emptyHTML = trustedTypesPolicy && RETURN_TRUSTED_TYPE ? trustedTypesPolicy.createHTML('') : '';

	  var _document = document,
	      implementation = _document.implementation,
	      createNodeIterator = _document.createNodeIterator,
	      getElementsByTagName = _document.getElementsByTagName,
	      createDocumentFragment = _document.createDocumentFragment;
	  var importNode = originalDocument.importNode;


	  var documentMode = {};
	  try {
	    documentMode = clone(document).documentMode ? document.documentMode : {};
	  } catch (_) {}

	  var hooks = {};

	  /**
	   * Expose whether this browser supports running the full DOMPurify.
	   */
	  DOMPurify.isSupported = implementation && typeof implementation.createHTMLDocument !== 'undefined' && documentMode !== 9;

	  var MUSTACHE_EXPR$$1 = MUSTACHE_EXPR,
	      ERB_EXPR$$1 = ERB_EXPR,
	      DATA_ATTR$$1 = DATA_ATTR,
	      ARIA_ATTR$$1 = ARIA_ATTR,
	      IS_SCRIPT_OR_DATA$$1 = IS_SCRIPT_OR_DATA,
	      ATTR_WHITESPACE$$1 = ATTR_WHITESPACE;
	  var IS_ALLOWED_URI$$1 = IS_ALLOWED_URI;

	  /**
	   * We consider the elements and attributes below to be safe. Ideally
	   * don't add any new ones but feel free to remove unwanted ones.
	   */

	  /* allowed element names */

	  var ALLOWED_TAGS = null;
	  var DEFAULT_ALLOWED_TAGS = addToSet({}, [].concat(_toConsumableArray$1(html), _toConsumableArray$1(svg), _toConsumableArray$1(svgFilters), _toConsumableArray$1(mathMl), _toConsumableArray$1(text)));

	  /* Allowed attribute names */
	  var ALLOWED_ATTR = null;
	  var DEFAULT_ALLOWED_ATTR = addToSet({}, [].concat(_toConsumableArray$1(html$1), _toConsumableArray$1(svg$1), _toConsumableArray$1(mathMl$1), _toConsumableArray$1(xml)));

	  /* Explicitly forbidden tags (overrides ALLOWED_TAGS/ADD_TAGS) */
	  var FORBID_TAGS = null;

	  /* Explicitly forbidden attributes (overrides ALLOWED_ATTR/ADD_ATTR) */
	  var FORBID_ATTR = null;

	  /* Decide if ARIA attributes are okay */
	  var ALLOW_ARIA_ATTR = true;

	  /* Decide if custom data attributes are okay */
	  var ALLOW_DATA_ATTR = true;

	  /* Decide if unknown protocols are okay */
	  var ALLOW_UNKNOWN_PROTOCOLS = false;

	  /* Output should be safe for common template engines.
	   * This means, DOMPurify removes data attributes, mustaches and ERB
	   */
	  var SAFE_FOR_TEMPLATES = false;

	  /* Decide if document with <html>... should be returned */
	  var WHOLE_DOCUMENT = false;

	  /* Track whether config is already set on this instance of DOMPurify. */
	  var SET_CONFIG = false;

	  /* Decide if all elements (e.g. style, script) must be children of
	   * document.body. By default, browsers might move them to document.head */
	  var FORCE_BODY = false;

	  /* Decide if a DOM `HTMLBodyElement` should be returned, instead of a html
	   * string (or a TrustedHTML object if Trusted Types are supported).
	   * If `WHOLE_DOCUMENT` is enabled a `HTMLHtmlElement` will be returned instead
	   */
	  var RETURN_DOM = false;

	  /* Decide if a DOM `DocumentFragment` should be returned, instead of a html
	   * string  (or a TrustedHTML object if Trusted Types are supported) */
	  var RETURN_DOM_FRAGMENT = false;

	  /* If `RETURN_DOM` or `RETURN_DOM_FRAGMENT` is enabled, decide if the returned DOM
	   * `Node` is imported into the current `Document`. If this flag is not enabled the
	   * `Node` will belong (its ownerDocument) to a fresh `HTMLDocument`, created by
	   * DOMPurify.
	   *
	   * This defaults to `true` starting DOMPurify 2.2.0. Note that setting it to `false`
	   * might cause XSS from attacks hidden in closed shadowroots in case the browser
	   * supports Declarative Shadow: DOM https://web.dev/declarative-shadow-dom/
	   */
	  var RETURN_DOM_IMPORT = true;

	  /* Try to return a Trusted Type object instead of a string, return a string in
	   * case Trusted Types are not supported  */
	  var RETURN_TRUSTED_TYPE = false;

	  /* Output should be free from DOM clobbering attacks? */
	  var SANITIZE_DOM = true;

	  /* Keep element content when removing element? */
	  var KEEP_CONTENT = true;

	  /* If a `Node` is passed to sanitize(), then performs sanitization in-place instead
	   * of importing it into a new Document and returning a sanitized copy */
	  var IN_PLACE = false;

	  /* Allow usage of profiles like html, svg and mathMl */
	  var USE_PROFILES = {};

	  /* Tags to ignore content of when KEEP_CONTENT is true */
	  var FORBID_CONTENTS = addToSet({}, ['annotation-xml', 'audio', 'colgroup', 'desc', 'foreignobject', 'head', 'iframe', 'math', 'mi', 'mn', 'mo', 'ms', 'mtext', 'noembed', 'noframes', 'noscript', 'plaintext', 'script', 'style', 'svg', 'template', 'thead', 'title', 'video', 'xmp']);

	  /* Tags that are safe for data: URIs */
	  var DATA_URI_TAGS = null;
	  var DEFAULT_DATA_URI_TAGS = addToSet({}, ['audio', 'video', 'img', 'source', 'image', 'track']);

	  /* Attributes safe for values like "javascript:" */
	  var URI_SAFE_ATTRIBUTES = null;
	  var DEFAULT_URI_SAFE_ATTRIBUTES = addToSet({}, ['alt', 'class', 'for', 'id', 'label', 'name', 'pattern', 'placeholder', 'summary', 'title', 'value', 'style', 'xmlns']);

	  /* Keep a reference to config to pass to hooks */
	  var CONFIG = null;

	  /* Ideally, do not touch anything below this line */
	  /* ______________________________________________ */

	  var formElement = document.createElement('form');

	  /**
	   * _parseConfig
	   *
	   * @param  {Object} cfg optional config literal
	   */
	  // eslint-disable-next-line complexity
	  var _parseConfig = function _parseConfig(cfg) {
	    if (CONFIG && CONFIG === cfg) {
	      return;
	    }

	    /* Shield configuration object from tampering */
	    if (!cfg || (typeof cfg === 'undefined' ? 'undefined' : _typeof(cfg)) !== 'object') {
	      cfg = {};
	    }

	    /* Shield configuration object from prototype pollution */
	    cfg = clone(cfg);

	    /* Set configuration parameters */
	    ALLOWED_TAGS = 'ALLOWED_TAGS' in cfg ? addToSet({}, cfg.ALLOWED_TAGS) : DEFAULT_ALLOWED_TAGS;
	    ALLOWED_ATTR = 'ALLOWED_ATTR' in cfg ? addToSet({}, cfg.ALLOWED_ATTR) : DEFAULT_ALLOWED_ATTR;
	    URI_SAFE_ATTRIBUTES = 'ADD_URI_SAFE_ATTR' in cfg ? addToSet(clone(DEFAULT_URI_SAFE_ATTRIBUTES), cfg.ADD_URI_SAFE_ATTR) : DEFAULT_URI_SAFE_ATTRIBUTES;
	    DATA_URI_TAGS = 'ADD_DATA_URI_TAGS' in cfg ? addToSet(clone(DEFAULT_DATA_URI_TAGS), cfg.ADD_DATA_URI_TAGS) : DEFAULT_DATA_URI_TAGS;
	    FORBID_TAGS = 'FORBID_TAGS' in cfg ? addToSet({}, cfg.FORBID_TAGS) : {};
	    FORBID_ATTR = 'FORBID_ATTR' in cfg ? addToSet({}, cfg.FORBID_ATTR) : {};
	    USE_PROFILES = 'USE_PROFILES' in cfg ? cfg.USE_PROFILES : false;
	    ALLOW_ARIA_ATTR = cfg.ALLOW_ARIA_ATTR !== false; // Default true
	    ALLOW_DATA_ATTR = cfg.ALLOW_DATA_ATTR !== false; // Default true
	    ALLOW_UNKNOWN_PROTOCOLS = cfg.ALLOW_UNKNOWN_PROTOCOLS || false; // Default false
	    SAFE_FOR_TEMPLATES = cfg.SAFE_FOR_TEMPLATES || false; // Default false
	    WHOLE_DOCUMENT = cfg.WHOLE_DOCUMENT || false; // Default false
	    RETURN_DOM = cfg.RETURN_DOM || false; // Default false
	    RETURN_DOM_FRAGMENT = cfg.RETURN_DOM_FRAGMENT || false; // Default false
	    RETURN_DOM_IMPORT = cfg.RETURN_DOM_IMPORT !== false; // Default true
	    RETURN_TRUSTED_TYPE = cfg.RETURN_TRUSTED_TYPE || false; // Default false
	    FORCE_BODY = cfg.FORCE_BODY || false; // Default false
	    SANITIZE_DOM = cfg.SANITIZE_DOM !== false; // Default true
	    KEEP_CONTENT = cfg.KEEP_CONTENT !== false; // Default true
	    IN_PLACE = cfg.IN_PLACE || false; // Default false
	    IS_ALLOWED_URI$$1 = cfg.ALLOWED_URI_REGEXP || IS_ALLOWED_URI$$1;
	    if (SAFE_FOR_TEMPLATES) {
	      ALLOW_DATA_ATTR = false;
	    }

	    if (RETURN_DOM_FRAGMENT) {
	      RETURN_DOM = true;
	    }

	    /* Parse profile info */
	    if (USE_PROFILES) {
	      ALLOWED_TAGS = addToSet({}, [].concat(_toConsumableArray$1(text)));
	      ALLOWED_ATTR = [];
	      if (USE_PROFILES.html === true) {
	        addToSet(ALLOWED_TAGS, html);
	        addToSet(ALLOWED_ATTR, html$1);
	      }

	      if (USE_PROFILES.svg === true) {
	        addToSet(ALLOWED_TAGS, svg);
	        addToSet(ALLOWED_ATTR, svg$1);
	        addToSet(ALLOWED_ATTR, xml);
	      }

	      if (USE_PROFILES.svgFilters === true) {
	        addToSet(ALLOWED_TAGS, svgFilters);
	        addToSet(ALLOWED_ATTR, svg$1);
	        addToSet(ALLOWED_ATTR, xml);
	      }

	      if (USE_PROFILES.mathMl === true) {
	        addToSet(ALLOWED_TAGS, mathMl);
	        addToSet(ALLOWED_ATTR, mathMl$1);
	        addToSet(ALLOWED_ATTR, xml);
	      }
	    }

	    /* Merge configuration parameters */
	    if (cfg.ADD_TAGS) {
	      if (ALLOWED_TAGS === DEFAULT_ALLOWED_TAGS) {
	        ALLOWED_TAGS = clone(ALLOWED_TAGS);
	      }

	      addToSet(ALLOWED_TAGS, cfg.ADD_TAGS);
	    }

	    if (cfg.ADD_ATTR) {
	      if (ALLOWED_ATTR === DEFAULT_ALLOWED_ATTR) {
	        ALLOWED_ATTR = clone(ALLOWED_ATTR);
	      }

	      addToSet(ALLOWED_ATTR, cfg.ADD_ATTR);
	    }

	    if (cfg.ADD_URI_SAFE_ATTR) {
	      addToSet(URI_SAFE_ATTRIBUTES, cfg.ADD_URI_SAFE_ATTR);
	    }

	    /* Add #text in case KEEP_CONTENT is set to true */
	    if (KEEP_CONTENT) {
	      ALLOWED_TAGS['#text'] = true;
	    }

	    /* Add html, head and body to ALLOWED_TAGS in case WHOLE_DOCUMENT is true */
	    if (WHOLE_DOCUMENT) {
	      addToSet(ALLOWED_TAGS, ['html', 'head', 'body']);
	    }

	    /* Add tbody to ALLOWED_TAGS in case tables are permitted, see #286, #365 */
	    if (ALLOWED_TAGS.table) {
	      addToSet(ALLOWED_TAGS, ['tbody']);
	      delete FORBID_TAGS.tbody;
	    }

	    // Prevent further manipulation of configuration.
	    // Not available in IE8, Safari 5, etc.
	    if (freeze) {
	      freeze(cfg);
	    }

	    CONFIG = cfg;
	  };

	  var MATHML_TEXT_INTEGRATION_POINTS = addToSet({}, ['mi', 'mo', 'mn', 'ms', 'mtext']);

	  var HTML_INTEGRATION_POINTS = addToSet({}, ['foreignobject', 'desc', 'title', 'annotation-xml']);

	  /* Keep track of all possible SVG and MathML tags
	   * so that we can perform the namespace checks
	   * correctly. */
	  var ALL_SVG_TAGS = addToSet({}, svg);
	  addToSet(ALL_SVG_TAGS, svgFilters);
	  addToSet(ALL_SVG_TAGS, svgDisallowed);

	  var ALL_MATHML_TAGS = addToSet({}, mathMl);
	  addToSet(ALL_MATHML_TAGS, mathMlDisallowed);

	  var MATHML_NAMESPACE = 'http://www.w3.org/1998/Math/MathML';
	  var SVG_NAMESPACE = 'http://www.w3.org/2000/svg';
	  var HTML_NAMESPACE = 'http://www.w3.org/1999/xhtml';

	  /**
	   *
	   *
	   * @param  {Element} element a DOM element whose namespace is being checked
	   * @returns {boolean} Return false if the element has a
	   *  namespace that a spec-compliant parser would never
	   *  return. Return true otherwise.
	   */
	  var _checkValidNamespace = function _checkValidNamespace(element) {
	    var parent = getParentNode(element);

	    // In JSDOM, if we're inside shadow DOM, then parentNode
	    // can be null. We just simulate parent in this case.
	    if (!parent || !parent.tagName) {
	      parent = {
	        namespaceURI: HTML_NAMESPACE,
	        tagName: 'template'
	      };
	    }

	    var tagName = stringToLowerCase(element.tagName);
	    var parentTagName = stringToLowerCase(parent.tagName);

	    if (element.namespaceURI === SVG_NAMESPACE) {
	      // The only way to switch from HTML namespace to SVG
	      // is via <svg>. If it happens via any other tag, then
	      // it should be killed.
	      if (parent.namespaceURI === HTML_NAMESPACE) {
	        return tagName === 'svg';
	      }

	      // The only way to switch from MathML to SVG is via
	      // svg if parent is either <annotation-xml> or MathML
	      // text integration points.
	      if (parent.namespaceURI === MATHML_NAMESPACE) {
	        return tagName === 'svg' && (parentTagName === 'annotation-xml' || MATHML_TEXT_INTEGRATION_POINTS[parentTagName]);
	      }

	      // We only allow elements that are defined in SVG
	      // spec. All others are disallowed in SVG namespace.
	      return Boolean(ALL_SVG_TAGS[tagName]);
	    }

	    if (element.namespaceURI === MATHML_NAMESPACE) {
	      // The only way to switch from HTML namespace to MathML
	      // is via <math>. If it happens via any other tag, then
	      // it should be killed.
	      if (parent.namespaceURI === HTML_NAMESPACE) {
	        return tagName === 'math';
	      }

	      // The only way to switch from SVG to MathML is via
	      // <math> and HTML integration points
	      if (parent.namespaceURI === SVG_NAMESPACE) {
	        return tagName === 'math' && HTML_INTEGRATION_POINTS[parentTagName];
	      }

	      // We only allow elements that are defined in MathML
	      // spec. All others are disallowed in MathML namespace.
	      return Boolean(ALL_MATHML_TAGS[tagName]);
	    }

	    if (element.namespaceURI === HTML_NAMESPACE) {
	      // The only way to switch from SVG to HTML is via
	      // HTML integration points, and from MathML to HTML
	      // is via MathML text integration points
	      if (parent.namespaceURI === SVG_NAMESPACE && !HTML_INTEGRATION_POINTS[parentTagName]) {
	        return false;
	      }

	      if (parent.namespaceURI === MATHML_NAMESPACE && !MATHML_TEXT_INTEGRATION_POINTS[parentTagName]) {
	        return false;
	      }

	      // Certain elements are allowed in both SVG and HTML
	      // namespace. We need to specify them explicitly
	      // so that they don't get erronously deleted from
	      // HTML namespace.
	      var commonSvgAndHTMLElements = addToSet({}, ['title', 'style', 'font', 'a', 'script']);

	      // We disallow tags that are specific for MathML
	      // or SVG and should never appear in HTML namespace
	      return !ALL_MATHML_TAGS[tagName] && (commonSvgAndHTMLElements[tagName] || !ALL_SVG_TAGS[tagName]);
	    }

	    // The code should never reach this place (this means
	    // that the element somehow got namespace that is not
	    // HTML, SVG or MathML). Return false just in case.
	    return false;
	  };

	  /**
	   * _forceRemove
	   *
	   * @param  {Node} node a DOM node
	   */
	  var _forceRemove = function _forceRemove(node) {
	    arrayPush(DOMPurify.removed, { element: node });
	    try {
	      node.parentNode.removeChild(node);
	    } catch (_) {
	      try {
	        node.outerHTML = emptyHTML;
	      } catch (_) {
	        node.remove();
	      }
	    }
	  };

	  /**
	   * _removeAttribute
	   *
	   * @param  {String} name an Attribute name
	   * @param  {Node} node a DOM node
	   */
	  var _removeAttribute = function _removeAttribute(name, node) {
	    try {
	      arrayPush(DOMPurify.removed, {
	        attribute: node.getAttributeNode(name),
	        from: node
	      });
	    } catch (_) {
	      arrayPush(DOMPurify.removed, {
	        attribute: null,
	        from: node
	      });
	    }

	    node.removeAttribute(name);
	  };

	  /**
	   * _initDocument
	   *
	   * @param  {String} dirty a string of dirty markup
	   * @return {Document} a DOM, filled with the dirty markup
	   */
	  var _initDocument = function _initDocument(dirty) {
	    /* Create a HTML document */
	    var doc = void 0;
	    var leadingWhitespace = void 0;

	    if (FORCE_BODY) {
	      dirty = '<remove></remove>' + dirty;
	    } else {
	      /* If FORCE_BODY isn't used, leading whitespace needs to be preserved manually */
	      var matches = stringMatch(dirty, /^[\r\n\t ]+/);
	      leadingWhitespace = matches && matches[0];
	    }

	    var dirtyPayload = trustedTypesPolicy ? trustedTypesPolicy.createHTML(dirty) : dirty;
	    /* Use the DOMParser API by default, fallback later if needs be */
	    try {
	      doc = new DOMParser().parseFromString(dirtyPayload, 'text/html');
	    } catch (_) {}

	    /* Use createHTMLDocument in case DOMParser is not available */
	    if (!doc || !doc.documentElement) {
	      doc = implementation.createHTMLDocument('');
	      var _doc = doc,
	          body = _doc.body;

	      body.parentNode.removeChild(body.parentNode.firstElementChild);
	      body.outerHTML = dirtyPayload;
	    }

	    if (dirty && leadingWhitespace) {
	      doc.body.insertBefore(document.createTextNode(leadingWhitespace), doc.body.childNodes[0] || null);
	    }

	    /* Work on whole document or just its body */
	    return getElementsByTagName.call(doc, WHOLE_DOCUMENT ? 'html' : 'body')[0];
	  };

	  /**
	   * _createIterator
	   *
	   * @param  {Document} root document/fragment to create iterator for
	   * @return {Iterator} iterator instance
	   */
	  var _createIterator = function _createIterator(root) {
	    return createNodeIterator.call(root.ownerDocument || root, root, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_COMMENT | NodeFilter.SHOW_TEXT, function () {
	      return NodeFilter.FILTER_ACCEPT;
	    }, false);
	  };

	  /**
	   * _isClobbered
	   *
	   * @param  {Node} elm element to check for clobbering attacks
	   * @return {Boolean} true if clobbered, false if safe
	   */
	  var _isClobbered = function _isClobbered(elm) {
	    if (elm instanceof Text || elm instanceof Comment) {
	      return false;
	    }

	    if (typeof elm.nodeName !== 'string' || typeof elm.textContent !== 'string' || typeof elm.removeChild !== 'function' || !(elm.attributes instanceof NamedNodeMap) || typeof elm.removeAttribute !== 'function' || typeof elm.setAttribute !== 'function' || typeof elm.namespaceURI !== 'string' || typeof elm.insertBefore !== 'function') {
	      return true;
	    }

	    return false;
	  };

	  /**
	   * _isNode
	   *
	   * @param  {Node} obj object to check whether it's a DOM node
	   * @return {Boolean} true is object is a DOM node
	   */
	  var _isNode = function _isNode(object) {
	    return (typeof Node === 'undefined' ? 'undefined' : _typeof(Node)) === 'object' ? object instanceof Node : object && (typeof object === 'undefined' ? 'undefined' : _typeof(object)) === 'object' && typeof object.nodeType === 'number' && typeof object.nodeName === 'string';
	  };

	  /**
	   * _executeHook
	   * Execute user configurable hooks
	   *
	   * @param  {String} entryPoint  Name of the hook's entry point
	   * @param  {Node} currentNode node to work on with the hook
	   * @param  {Object} data additional hook parameters
	   */
	  var _executeHook = function _executeHook(entryPoint, currentNode, data) {
	    if (!hooks[entryPoint]) {
	      return;
	    }

	    arrayForEach(hooks[entryPoint], function (hook) {
	      hook.call(DOMPurify, currentNode, data, CONFIG);
	    });
	  };

	  /**
	   * _sanitizeElements
	   *
	   * @protect nodeName
	   * @protect textContent
	   * @protect removeChild
	   *
	   * @param   {Node} currentNode to check for permission to exist
	   * @return  {Boolean} true if node was killed, false if left alive
	   */
	  var _sanitizeElements = function _sanitizeElements(currentNode) {
	    var content = void 0;

	    /* Execute a hook if present */
	    _executeHook('beforeSanitizeElements', currentNode, null);

	    /* Check if element is clobbered or can clobber */
	    if (_isClobbered(currentNode)) {
	      _forceRemove(currentNode);
	      return true;
	    }

	    /* Check if tagname contains Unicode */
	    if (stringMatch(currentNode.nodeName, /[\u0080-\uFFFF]/)) {
	      _forceRemove(currentNode);
	      return true;
	    }

	    /* Now let's check the element's type and name */
	    var tagName = stringToLowerCase(currentNode.nodeName);

	    /* Execute a hook if present */
	    _executeHook('uponSanitizeElement', currentNode, {
	      tagName: tagName,
	      allowedTags: ALLOWED_TAGS
	    });

	    /* Detect mXSS attempts abusing namespace confusion */
	    if (!_isNode(currentNode.firstElementChild) && (!_isNode(currentNode.content) || !_isNode(currentNode.content.firstElementChild)) && regExpTest(/<[/\w]/g, currentNode.innerHTML) && regExpTest(/<[/\w]/g, currentNode.textContent)) {
	      _forceRemove(currentNode);
	      return true;
	    }

	    /* Remove element if anything forbids its presence */
	    if (!ALLOWED_TAGS[tagName] || FORBID_TAGS[tagName]) {
	      /* Keep content except for bad-listed elements */
	      if (KEEP_CONTENT && !FORBID_CONTENTS[tagName]) {
	        var parentNode = getParentNode(currentNode);
	        var childNodes = getChildNodes(currentNode);
	        var childCount = childNodes.length;
	        for (var i = childCount - 1; i >= 0; --i) {
	          parentNode.insertBefore(cloneNode(childNodes[i], true), getNextSibling(currentNode));
	        }
	      }

	      _forceRemove(currentNode);
	      return true;
	    }

	    /* Check whether element has a valid namespace */
	    if (currentNode instanceof Element && !_checkValidNamespace(currentNode)) {
	      _forceRemove(currentNode);
	      return true;
	    }

	    if ((tagName === 'noscript' || tagName === 'noembed') && regExpTest(/<\/no(script|embed)/i, currentNode.innerHTML)) {
	      _forceRemove(currentNode);
	      return true;
	    }

	    /* Sanitize element content to be template-safe */
	    if (SAFE_FOR_TEMPLATES && currentNode.nodeType === 3) {
	      /* Get the element's text content */
	      content = currentNode.textContent;
	      content = stringReplace(content, MUSTACHE_EXPR$$1, ' ');
	      content = stringReplace(content, ERB_EXPR$$1, ' ');
	      if (currentNode.textContent !== content) {
	        arrayPush(DOMPurify.removed, { element: currentNode.cloneNode() });
	        currentNode.textContent = content;
	      }
	    }

	    /* Execute a hook if present */
	    _executeHook('afterSanitizeElements', currentNode, null);

	    return false;
	  };

	  /**
	   * _isValidAttribute
	   *
	   * @param  {string} lcTag Lowercase tag name of containing element.
	   * @param  {string} lcName Lowercase attribute name.
	   * @param  {string} value Attribute value.
	   * @return {Boolean} Returns true if `value` is valid, otherwise false.
	   */
	  // eslint-disable-next-line complexity
	  var _isValidAttribute = function _isValidAttribute(lcTag, lcName, value) {
	    /* Make sure attribute cannot clobber */
	    if (SANITIZE_DOM && (lcName === 'id' || lcName === 'name') && (value in document || value in formElement)) {
	      return false;
	    }

	    /* Allow valid data-* attributes: At least one character after "-"
	        (https://html.spec.whatwg.org/multipage/dom.html#embedding-custom-non-visible-data-with-the-data-*-attributes)
	        XML-compatible (https://html.spec.whatwg.org/multipage/infrastructure.html#xml-compatible and http://www.w3.org/TR/xml/#d0e804)
	        We don't need to check the value; it's always URI safe. */
	    if (ALLOW_DATA_ATTR && regExpTest(DATA_ATTR$$1, lcName)) ; else if (ALLOW_ARIA_ATTR && regExpTest(ARIA_ATTR$$1, lcName)) ; else if (!ALLOWED_ATTR[lcName] || FORBID_ATTR[lcName]) {
	      return false;

	      /* Check value is safe. First, is attr inert? If so, is safe */
	    } else if (URI_SAFE_ATTRIBUTES[lcName]) ; else if (regExpTest(IS_ALLOWED_URI$$1, stringReplace(value, ATTR_WHITESPACE$$1, ''))) ; else if ((lcName === 'src' || lcName === 'xlink:href' || lcName === 'href') && lcTag !== 'script' && stringIndexOf(value, 'data:') === 0 && DATA_URI_TAGS[lcTag]) ; else if (ALLOW_UNKNOWN_PROTOCOLS && !regExpTest(IS_SCRIPT_OR_DATA$$1, stringReplace(value, ATTR_WHITESPACE$$1, ''))) ; else if (!value) ; else {
	      return false;
	    }

	    return true;
	  };

	  /**
	   * _sanitizeAttributes
	   *
	   * @protect attributes
	   * @protect nodeName
	   * @protect removeAttribute
	   * @protect setAttribute
	   *
	   * @param  {Node} currentNode to sanitize
	   */
	  var _sanitizeAttributes = function _sanitizeAttributes(currentNode) {
	    var attr = void 0;
	    var value = void 0;
	    var lcName = void 0;
	    var l = void 0;
	    /* Execute a hook if present */
	    _executeHook('beforeSanitizeAttributes', currentNode, null);

	    var attributes = currentNode.attributes;

	    /* Check if we have attributes; if not we might have a text node */

	    if (!attributes) {
	      return;
	    }

	    var hookEvent = {
	      attrName: '',
	      attrValue: '',
	      keepAttr: true,
	      allowedAttributes: ALLOWED_ATTR
	    };
	    l = attributes.length;

	    /* Go backwards over all attributes; safely remove bad ones */
	    while (l--) {
	      attr = attributes[l];
	      var _attr = attr,
	          name = _attr.name,
	          namespaceURI = _attr.namespaceURI;

	      value = stringTrim(attr.value);
	      lcName = stringToLowerCase(name);

	      /* Execute a hook if present */
	      hookEvent.attrName = lcName;
	      hookEvent.attrValue = value;
	      hookEvent.keepAttr = true;
	      hookEvent.forceKeepAttr = undefined; // Allows developers to see this is a property they can set
	      _executeHook('uponSanitizeAttribute', currentNode, hookEvent);
	      value = hookEvent.attrValue;
	      /* Did the hooks approve of the attribute? */
	      if (hookEvent.forceKeepAttr) {
	        continue;
	      }

	      /* Remove attribute */
	      _removeAttribute(name, currentNode);

	      /* Did the hooks approve of the attribute? */
	      if (!hookEvent.keepAttr) {
	        continue;
	      }

	      /* Work around a security issue in jQuery 3.0 */
	      if (regExpTest(/\/>/i, value)) {
	        _removeAttribute(name, currentNode);
	        continue;
	      }

	      /* Sanitize attribute content to be template-safe */
	      if (SAFE_FOR_TEMPLATES) {
	        value = stringReplace(value, MUSTACHE_EXPR$$1, ' ');
	        value = stringReplace(value, ERB_EXPR$$1, ' ');
	      }

	      /* Is `value` valid for this attribute? */
	      var lcTag = currentNode.nodeName.toLowerCase();
	      if (!_isValidAttribute(lcTag, lcName, value)) {
	        continue;
	      }

	      /* Handle invalid data-* attribute set by try-catching it */
	      try {
	        if (namespaceURI) {
	          currentNode.setAttributeNS(namespaceURI, name, value);
	        } else {
	          /* Fallback to setAttribute() for browser-unrecognized namespaces e.g. "x-schema". */
	          currentNode.setAttribute(name, value);
	        }

	        arrayPop(DOMPurify.removed);
	      } catch (_) {}
	    }

	    /* Execute a hook if present */
	    _executeHook('afterSanitizeAttributes', currentNode, null);
	  };

	  /**
	   * _sanitizeShadowDOM
	   *
	   * @param  {DocumentFragment} fragment to iterate over recursively
	   */
	  var _sanitizeShadowDOM = function _sanitizeShadowDOM(fragment) {
	    var shadowNode = void 0;
	    var shadowIterator = _createIterator(fragment);

	    /* Execute a hook if present */
	    _executeHook('beforeSanitizeShadowDOM', fragment, null);

	    while (shadowNode = shadowIterator.nextNode()) {
	      /* Execute a hook if present */
	      _executeHook('uponSanitizeShadowNode', shadowNode, null);

	      /* Sanitize tags and elements */
	      if (_sanitizeElements(shadowNode)) {
	        continue;
	      }

	      /* Deep shadow DOM detected */
	      if (shadowNode.content instanceof DocumentFragment) {
	        _sanitizeShadowDOM(shadowNode.content);
	      }

	      /* Check attributes, sanitize if necessary */
	      _sanitizeAttributes(shadowNode);
	    }

	    /* Execute a hook if present */
	    _executeHook('afterSanitizeShadowDOM', fragment, null);
	  };

	  /**
	   * Sanitize
	   * Public method providing core sanitation functionality
	   *
	   * @param {String|Node} dirty string or DOM node
	   * @param {Object} configuration object
	   */
	  // eslint-disable-next-line complexity
	  DOMPurify.sanitize = function (dirty, cfg) {
	    var body = void 0;
	    var importedNode = void 0;
	    var currentNode = void 0;
	    var oldNode = void 0;
	    var returnNode = void 0;
	    /* Make sure we have a string to sanitize.
	      DO NOT return early, as this will return the wrong type if
	      the user has requested a DOM object rather than a string */
	    if (!dirty) {
	      dirty = '<!-->';
	    }

	    /* Stringify, in case dirty is an object */
	    if (typeof dirty !== 'string' && !_isNode(dirty)) {
	      // eslint-disable-next-line no-negated-condition
	      if (typeof dirty.toString !== 'function') {
	        throw typeErrorCreate('toString is not a function');
	      } else {
	        dirty = dirty.toString();
	        if (typeof dirty !== 'string') {
	          throw typeErrorCreate('dirty is not a string, aborting');
	        }
	      }
	    }

	    /* Check we can run. Otherwise fall back or ignore */
	    if (!DOMPurify.isSupported) {
	      if (_typeof(window.toStaticHTML) === 'object' || typeof window.toStaticHTML === 'function') {
	        if (typeof dirty === 'string') {
	          return window.toStaticHTML(dirty);
	        }

	        if (_isNode(dirty)) {
	          return window.toStaticHTML(dirty.outerHTML);
	        }
	      }

	      return dirty;
	    }

	    /* Assign config vars */
	    if (!SET_CONFIG) {
	      _parseConfig(cfg);
	    }

	    /* Clean up removed elements */
	    DOMPurify.removed = [];

	    /* Check if dirty is correctly typed for IN_PLACE */
	    if (typeof dirty === 'string') {
	      IN_PLACE = false;
	    }

	    if (IN_PLACE) ; else if (dirty instanceof Node) {
	      /* If dirty is a DOM element, append to an empty document to avoid
	         elements being stripped by the parser */
	      body = _initDocument('<!---->');
	      importedNode = body.ownerDocument.importNode(dirty, true);
	      if (importedNode.nodeType === 1 && importedNode.nodeName === 'BODY') {
	        /* Node is already a body, use as is */
	        body = importedNode;
	      } else if (importedNode.nodeName === 'HTML') {
	        body = importedNode;
	      } else {
	        // eslint-disable-next-line unicorn/prefer-node-append
	        body.appendChild(importedNode);
	      }
	    } else {
	      /* Exit directly if we have nothing to do */
	      if (!RETURN_DOM && !SAFE_FOR_TEMPLATES && !WHOLE_DOCUMENT &&
	      // eslint-disable-next-line unicorn/prefer-includes
	      dirty.indexOf('<') === -1) {
	        return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? trustedTypesPolicy.createHTML(dirty) : dirty;
	      }

	      /* Initialize the document to work on */
	      body = _initDocument(dirty);

	      /* Check we have a DOM node from the data */
	      if (!body) {
	        return RETURN_DOM ? null : emptyHTML;
	      }
	    }

	    /* Remove first element node (ours) if FORCE_BODY is set */
	    if (body && FORCE_BODY) {
	      _forceRemove(body.firstChild);
	    }

	    /* Get node iterator */
	    var nodeIterator = _createIterator(IN_PLACE ? dirty : body);

	    /* Now start iterating over the created document */
	    while (currentNode = nodeIterator.nextNode()) {
	      /* Fix IE's strange behavior with manipulated textNodes #89 */
	      if (currentNode.nodeType === 3 && currentNode === oldNode) {
	        continue;
	      }

	      /* Sanitize tags and elements */
	      if (_sanitizeElements(currentNode)) {
	        continue;
	      }

	      /* Shadow DOM detected, sanitize it */
	      if (currentNode.content instanceof DocumentFragment) {
	        _sanitizeShadowDOM(currentNode.content);
	      }

	      /* Check attributes, sanitize if necessary */
	      _sanitizeAttributes(currentNode);

	      oldNode = currentNode;
	    }

	    oldNode = null;

	    /* If we sanitized `dirty` in-place, return it. */
	    if (IN_PLACE) {
	      return dirty;
	    }

	    /* Return sanitized string or DOM */
	    if (RETURN_DOM) {
	      if (RETURN_DOM_FRAGMENT) {
	        returnNode = createDocumentFragment.call(body.ownerDocument);

	        while (body.firstChild) {
	          // eslint-disable-next-line unicorn/prefer-node-append
	          returnNode.appendChild(body.firstChild);
	        }
	      } else {
	        returnNode = body;
	      }

	      if (RETURN_DOM_IMPORT) {
	        /*
	          AdoptNode() is not used because internal state is not reset
	          (e.g. the past names map of a HTMLFormElement), this is safe
	          in theory but we would rather not risk another attack vector.
	          The state that is cloned by importNode() is explicitly defined
	          by the specs.
	        */
	        returnNode = importNode.call(originalDocument, returnNode, true);
	      }

	      return returnNode;
	    }

	    var serializedHTML = WHOLE_DOCUMENT ? body.outerHTML : body.innerHTML;

	    /* Sanitize final string template-safe */
	    if (SAFE_FOR_TEMPLATES) {
	      serializedHTML = stringReplace(serializedHTML, MUSTACHE_EXPR$$1, ' ');
	      serializedHTML = stringReplace(serializedHTML, ERB_EXPR$$1, ' ');
	    }

	    return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? trustedTypesPolicy.createHTML(serializedHTML) : serializedHTML;
	  };

	  /**
	   * Public method to set the configuration once
	   * setConfig
	   *
	   * @param {Object} cfg configuration object
	   */
	  DOMPurify.setConfig = function (cfg) {
	    _parseConfig(cfg);
	    SET_CONFIG = true;
	  };

	  /**
	   * Public method to remove the configuration
	   * clearConfig
	   *
	   */
	  DOMPurify.clearConfig = function () {
	    CONFIG = null;
	    SET_CONFIG = false;
	  };

	  /**
	   * Public method to check if an attribute value is valid.
	   * Uses last set config, if any. Otherwise, uses config defaults.
	   * isValidAttribute
	   *
	   * @param  {string} tag Tag name of containing element.
	   * @param  {string} attr Attribute name.
	   * @param  {string} value Attribute value.
	   * @return {Boolean} Returns true if `value` is valid. Otherwise, returns false.
	   */
	  DOMPurify.isValidAttribute = function (tag, attr, value) {
	    /* Initialize shared config vars if necessary. */
	    if (!CONFIG) {
	      _parseConfig({});
	    }

	    var lcTag = stringToLowerCase(tag);
	    var lcName = stringToLowerCase(attr);
	    return _isValidAttribute(lcTag, lcName, value);
	  };

	  /**
	   * AddHook
	   * Public method to add DOMPurify hooks
	   *
	   * @param {String} entryPoint entry point for the hook to add
	   * @param {Function} hookFunction function to execute
	   */
	  DOMPurify.addHook = function (entryPoint, hookFunction) {
	    if (typeof hookFunction !== 'function') {
	      return;
	    }

	    hooks[entryPoint] = hooks[entryPoint] || [];
	    arrayPush(hooks[entryPoint], hookFunction);
	  };

	  /**
	   * RemoveHook
	   * Public method to remove a DOMPurify hook at a given entryPoint
	   * (pops it from the stack of hooks if more are present)
	   *
	   * @param {String} entryPoint entry point for the hook to remove
	   */
	  DOMPurify.removeHook = function (entryPoint) {
	    if (hooks[entryPoint]) {
	      arrayPop(hooks[entryPoint]);
	    }
	  };

	  /**
	   * RemoveHooks
	   * Public method to remove all DOMPurify hooks at a given entryPoint
	   *
	   * @param  {String} entryPoint entry point for the hooks to remove
	   */
	  DOMPurify.removeHooks = function (entryPoint) {
	    if (hooks[entryPoint]) {
	      hooks[entryPoint] = [];
	    }
	  };

	  /**
	   * RemoveAllHooks
	   * Public method to remove all DOMPurify hooks
	   *
	   */
	  DOMPurify.removeAllHooks = function () {
	    hooks = {};
	  };

	  return DOMPurify;
	}

	var purify = createDOMPurify();

	var globalWin  = window;
	var globalDoc  = document;

	var IMAGE_MIME_REGEX = /^image\/(p?jpe?g|gif|png|bmp)$/i;

	/**
	 * Wrap inlines that are in the root in paragraphs.
	 *
	 * @param {HTMLBodyElement} body
	 * @param {Document} doc
	 * @private
	 */
	function wrapInlines(body, doc) {
		var wrapper;

		traverse(body, function (node) {
			if (isInline(node, true)) {
				// Ignore text nodes unless they contain non-whitespace chars as
				// whitespace will be collapsed.
				// Ignore sceditor-ignore elements unless wrapping siblings
				// Should still wrap both if wrapping siblings.
				if (wrapper || node.nodeType === TEXT_NODE ?
					/\S/.test(node.nodeValue) : !is(node, '.sceditor-ignore')) {
					if (!wrapper) {
						wrapper = createElement('p', {}, doc);
						insertBefore(wrapper, node);
					}

					appendChild(wrapper, node);
				}
			} else {
				wrapper = null;
			}
		}, false, true);
	}
	/**
	 * SCEditor - A lightweight WYSIWYG editor
	 *
	 * @param {HTMLTextAreaElement} original The textarea to be converted
	 * @param {Object} userOptions
	 * @class SCEditor
	 * @name SCEditor
	 */
	function SCEditor(original, userOptions) {
		/**
		 * Alias of this
		 *
		 * @private
		 */
		var base = this;

		/**
		 * Editor format like BBCode or HTML
		 */
		var format;

		/**
		 * The div which contains the editor and toolbar
		 *
		 * @type {HTMLDivElement}
		 * @private
		 */
		var editorContainer;

		/**
		 * Map of events handlers bound to this instance.
		 *
		 * @type {Object}
		 * @private
		 */
		var eventHandlers = {};

		/**
		 * The editors toolbar
		 *
		 * @type {HTMLDivElement}
		 * @private
		 */
		var toolbar;

		/**
		 * The editors iframe which should be in design mode
		 *
		 * @type {HTMLIFrameElement}
		 * @private
		 */
		var wysiwygEditor;

		/**
		 * The editors window
		 *
		 * @type {Window}
		 * @private
		 */
		var wysiwygWindow;

		/**
		 * The WYSIWYG editors body element
		 *
		 * @type {HTMLBodyElement}
		 * @private
		 */
		var wysiwygBody;

		/**
		 * The WYSIWYG editors document
		 *
		 * @type {Document}
		 * @private
		 */
		var wysiwygDocument;

		/**
		 * The editors textarea for viewing source
		 *
		 * @type {HTMLTextAreaElement}
		 * @private
		 */
		var sourceEditor;

		/**
		 * The current dropdown
		 *
		 * @type {HTMLDivElement}
		 * @private
		 */
		var dropdown;

		/**
		 * If the user is currently composing text via IME
		 * @type {boolean}
		 */
		var isComposing;

		/**
		 * Timer for valueChanged key handler
		 * @type {number}
		 */
		var valueChangedKeyUpTimer;

		/**
		 * The editors locale
		 *
		 * @private
		 */
		var locale;

		/**
		 * Stores a cache of preloaded images
		 *
		 * @private
		 * @type {Array.<HTMLImageElement>}
		 */
		var preLoadCache = [];

		/**
		 * The editors rangeHelper instance
		 *
		 * @type {RangeHelper}
		 * @private
		 */
		var rangeHelper;

		/**
		 * An array of button state handlers
		 *
		 * @type {Array.<Object>}
		 * @private
		 */
		var btnStateHandlers = [];

		/**
		 * Plugin manager instance
		 *
		 * @type {PluginManager}
		 * @private
		 */
		var pluginManager;

		/**
		 * The current node containing the selection/caret
		 *
		 * @type {Node}
		 * @private
		 */
		var currentNode;

		/**
		 * The first block level parent of the current node
		 *
		 * @type {node}
		 * @private
		 */
		var currentBlockNode;

		/**
		 * The current node selection/caret
		 *
		 * @type {Object}
		 * @private
		 */
		var currentSelection;

		/**
		 * Used to make sure only 1 selection changed
		 * check is called every 100ms.
		 *
		 * Helps improve performance as it is checked a lot.
		 *
		 * @type {boolean}
		 * @private
		 */
		var isSelectionCheckPending;

		/**
		 * If content is required (equivalent to the HTML5 required attribute)
		 *
		 * @type {boolean}
		 * @private
		 */
		var isRequired;

		/**
		 * The inline CSS style element. Will be undefined
		 * until css() is called for the first time.
		 *
		 * @type {HTMLStyleElement}
		 * @private
		 */
		var inlineCss;

		/**
		 * Object containing a list of shortcut handlers
		 *
		 * @type {Object}
		 * @private
		 */
		var shortcutHandlers = {};

		/**
		 * The min and max heights that autoExpand should stay within
		 *
		 * @type {Object}
		 * @private
		 */
		var autoExpandBounds;

		/**
		 * Timeout for the autoExpand function to throttle calls
		 *
		 * @private
		 */
		var autoExpandThrottle;

		/**
		 * Cache of the current toolbar buttons
		 *
		 * @type {Object}
		 * @private
		 */
		var toolbarButtons = {};

		/**
		 * Last scroll position before maximizing so
		 * it can be restored when finished.
		 *
		 * @type {number}
		 * @private
		 */
		var maximizeScrollPosition;

		/**
		 * Stores the contents while a paste is taking place.
		 *
		 * Needed to support browsers that lack clipboard API support.
		 *
		 * @type {?DocumentFragment}
		 * @private
		 */
		var pasteContentFragment;

		/**
		 * All the emoticons from dropdown, more and hidden combined
		 * and with the emoticons root set
		 *
		 * @type {!Object<string, string>}
		 * @private
		 */
		var allEmoticons = {};

		/**
		 * Current icon set if any
		 *
		 * @type {?Object}
		 * @private
		 */
		var icons;

		/**
		 * Private functions
		 * @private
		 */
		var	init,
			replaceEmoticons,
			handleCommand,
			initEditor,
			initLocale,
			initToolBar,
			initOptions,
			initEvents,
			initResize,
			initEmoticons,
			handlePasteEvt,
			handleCutCopyEvt,
			handlePasteData,
			handleKeyDown,
			handleBackSpace,
			handleKeyPress,
			handleFormReset,
			handleMouseDown,
			handleComposition,
			handleEvent,
			handleDocumentClick,
			updateToolBar,
			updateActiveButtons,
			sourceEditorSelectedText,
			appendNewLine,
			checkSelectionChanged,
			checkNodeChanged,
			autofocus,
			emoticonsKeyPress,
			emoticonsCheckWhitespace,
			currentStyledBlockNode,
			triggerValueChanged,
			valueChangedBlur,
			valueChangedKeyUp,
			autoUpdate,
			autoExpand;

		/**
		 * All the commands supported by the editor
		 * @name commands
		 * @memberOf SCEditor.prototype
		 */
		base.commands = extend(true, {}, (userOptions.commands || defaultCmds));

		/**
		 * Options for this editor instance
		 * @name opts
		 * @memberOf SCEditor.prototype
		 */
		var options = base.opts = extend(
			true, {}, defaultOptions, userOptions
		);

		// Don't deep extend emoticons (fixes #565)
		base.opts.emoticons = userOptions.emoticons || defaultOptions.emoticons;

		if (!Array.isArray(options.allowedIframeUrls)) {
			options.allowedIframeUrls = [];
		}
		options.allowedIframeUrls.push('https://www.youtube-nocookie.com/embed/');

		// Create new instance of DOMPurify for each editor instance so can
		// have different allowed iframe URLs
		// eslint-disable-next-line new-cap
		var domPurify = purify();

		// Allow iframes for things like YouTube, see:
		// https://github.com/cure53/DOMPurify/issues/340#issuecomment-670758980
		domPurify.addHook('uponSanitizeElement', function (node, data) {
			var allowedUrls = options.allowedIframeUrls;

			if (data.tagName === 'iframe') {
				var src = attr(node, 'src') || '';

				for (var i = 0; i < allowedUrls.length; i++) {
					var url = allowedUrls[i];

					if (isString(url) && src.substr(0, url.length) === url) {
						return;
					}

					// Handle regex
					if (url.test && url.test(src)) {
						return;
					}
				}

				// No match so remove
				remove(node);
			}
		});

		// Convert target attribute into data-sce-target attributes so XHTML format
		// can allow them
		domPurify.addHook('afterSanitizeAttributes', function (node) {
			if ('target' in node) {
				attr(node, 'data-sce-target', attr(node, 'target'));
			}

			removeAttr(node, 'target');
		});

		/**
		 * Sanitize HTML to avoid XSS
		 *
		 * @param {string} html
		 * @return {string} html
		 * @private
		 */
		function sanitize(html) {
			return domPurify.sanitize(html, {
				ADD_TAGS: ['iframe'],
				ADD_ATTR: ['allowfullscreen', 'frameborder', 'target']
			});
		}
		/**
		 * Creates the editor iframe and textarea
		 * @private
		 */
		init = function () {
			original._sceditor = base;

			// Load locale
			if (options.locale && options.locale !== 'en') {
				initLocale();
			}

			editorContainer = createElement('div', {
				className: 'sceditor-container'
			});

			insertBefore(editorContainer, original);
			css(editorContainer, 'z-index', options.zIndex);

			isRequired = original.required;
			original.required = false;

			var FormatCtor = SCEditor.formats[options.format];
			format = FormatCtor ? new FormatCtor() : {};
			/*
			 * Plugins should be initialized before the formatters since
			 * they may wish to add or change formatting handlers and
			 * since the bbcode format caches its handlers,
			 * such changes must be done first.
			 */
			pluginManager = new PluginManager(base);
			(options.plugins || '').split(',').forEach(function (plugin) {
				pluginManager.register(plugin.trim());
			});
			if ('init' in format) {
				format.init.call(base);
			}

			// create the editor
			initEmoticons();
			initToolBar();
			initEditor();
			initOptions();
			initEvents();

			// force into source mode if is a browser that can't handle
			// full editing
			if (!isWysiwygSupported) {
				base.toggleSourceMode();
			}

			updateActiveButtons();

			var loaded = function () {
				off(globalWin, 'load', loaded);

				if (options.autofocus) {
					autofocus(!!options.autofocusEnd);
				}

				autoExpand();
				appendNewLine();
				// TODO: use editor doc and window?
				pluginManager.call('ready');
				if ('onReady' in format) {
					format.onReady.call(base);
				}
			};
			on(globalWin, 'load', loaded);
			if (globalDoc.readyState === 'complete') {
				loaded();
			}
		};

		/**
		 * Init the locale variable with the specified locale if possible
		 * @private
		 * @return void
		 */
		initLocale = function () {
			var lang;

			locale = SCEditor.locale[options.locale];

			if (!locale) {
				lang   = options.locale.split('-');
				locale = SCEditor.locale[lang[0]];
			}

			// Locale DateTime format overrides any specified in the options
			if (locale && locale.dateFormat) {
				options.dateFormat = locale.dateFormat;
			}
		};

		/**
		 * Creates the editor iframe and textarea
		 * @private
		 */
		initEditor = function () {
			sourceEditor  = createElement('textarea');
			wysiwygEditor = createElement('iframe', {
				frameborder: 0,
				allowfullscreen: true
			});

			/*
			 * This needs to be done right after they are created because,
			 * for any reason, the user may not want the value to be tinkered
			 * by any filters.
			 */
			if (options.startInSourceMode) {
				addClass(editorContainer, 'sourceMode');
				hide(wysiwygEditor);
			} else {
				addClass(editorContainer, 'wysiwygMode');
				hide(sourceEditor);
			}

			if (!options.spellcheck) {
				attr(editorContainer, 'spellcheck', 'false');
			}

			if (globalWin.location.protocol === 'https:') {
				attr(wysiwygEditor, 'src', 'about:blank');
			}

			// Add the editor to the container
			appendChild(editorContainer, wysiwygEditor);
			appendChild(editorContainer, sourceEditor);

			// TODO: make this optional somehow
			base.dimensions(
				options.width || width(original),
				options.height || height(original)
			);

			// Add ios to HTML so can apply CSS fix to only it
			var className = ios ? ' ios' : '';

			wysiwygDocument = wysiwygEditor.contentDocument;
			wysiwygDocument.open();
			wysiwygDocument.write(_tmpl('html', {
				attrs: ' class="' + className + '"',
				spellcheck: options.spellcheck ? '' : 'spellcheck="false"',
				charset: options.charset,
				style: options.style
			}));
			wysiwygDocument.close();

			wysiwygBody = wysiwygDocument.body;
			wysiwygWindow = wysiwygEditor.contentWindow;

			base.readOnly(!!options.readOnly);

			// iframe overflow fix for iOS
			if (ios) {
				height(wysiwygBody, '100%');
				on(wysiwygBody, 'touchend', base.focus);
			}

			var tabIndex = attr(original, 'tabindex');
			attr(sourceEditor, 'tabindex', tabIndex);
			attr(wysiwygEditor, 'tabindex', tabIndex);

			rangeHelper = new RangeHelper(wysiwygWindow, null, sanitize);

			// load any textarea value into the editor
			hide(original);
			base.val(original.value);

			var placeholder = options.placeholder ||
				attr(original, 'placeholder');

			if (placeholder) {
				sourceEditor.placeholder = placeholder;
				attr(wysiwygBody, 'placeholder', placeholder);
			}
		};

		/**
		 * Initialises options
		 * @private
		 */
		initOptions = function () {
			// auto-update original textbox on blur if option set to true
			if (options.autoUpdate) {
				on(wysiwygBody, 'blur', autoUpdate);
				on(sourceEditor, 'blur', autoUpdate);
			}

			if (options.rtl === null) {
				options.rtl = css(sourceEditor, 'direction') === 'rtl';
			}

			base.rtl(!!options.rtl);

			if (options.autoExpand) {
				// Need to update when images (or anything else) loads
				on(wysiwygBody, 'load', autoExpand, EVENT_CAPTURE);
				on(wysiwygBody, 'input keyup', autoExpand);
			}

			if (options.resizeEnabled) {
				initResize();
			}

			attr(editorContainer, 'id', options.id);
			base.emoticons(options.emoticonsEnabled);
		};

		/**
		 * Initialises events
		 * @private
		 */
		initEvents = function () {
			var form = original.form;
			var compositionEvents = 'compositionstart compositionend';
			var eventsToForward =
				'keydown keyup keypress focus blur contextmenu input';
			var checkSelectionEvents = 'onselectionchange' in wysiwygDocument ?
				'selectionchange' :
				'keyup focus blur contextmenu mouseup touchend click';

			on(globalDoc, 'click', handleDocumentClick);

			if (form) {
				on(form, 'reset', handleFormReset);
				on(form, 'submit', base.updateOriginal, EVENT_CAPTURE);
			}

			on(window, 'pagehide', base.updateOriginal);
			on(window, 'pageshow', handleFormReset);
			on(wysiwygBody, 'keypress', handleKeyPress);
			on(wysiwygBody, 'keydown', handleKeyDown);
			on(wysiwygBody, 'keydown', handleBackSpace);
			on(wysiwygBody, 'keyup', appendNewLine);
			on(wysiwygBody, 'blur', valueChangedBlur);
			on(wysiwygBody, 'keyup', valueChangedKeyUp);
			on(wysiwygBody, 'paste', handlePasteEvt);
			on(wysiwygBody, 'cut copy', handleCutCopyEvt);
			on(wysiwygBody, compositionEvents, handleComposition);
			on(wysiwygBody, checkSelectionEvents, checkSelectionChanged);
			on(wysiwygBody, eventsToForward, handleEvent);

			if (options.emoticonsCompat && globalWin.getSelection) {
				on(wysiwygBody, 'keyup', emoticonsCheckWhitespace);
			}

			on(wysiwygBody, 'blur', function () {
				if (!base.val()) {
					addClass(wysiwygBody, 'placeholder');
				}
			});

			on(wysiwygBody, 'focus', function () {
				removeClass(wysiwygBody, 'placeholder');
			});

			on(sourceEditor, 'blur', valueChangedBlur);
			on(sourceEditor, 'keyup', valueChangedKeyUp);
			on(sourceEditor, 'keydown', handleKeyDown);
			on(sourceEditor, compositionEvents, handleComposition);
			on(sourceEditor, eventsToForward, handleEvent);

			on(wysiwygDocument, 'mousedown', handleMouseDown);
			on(wysiwygDocument, checkSelectionEvents, checkSelectionChanged);
			on(wysiwygDocument, 'keyup', appendNewLine);

			on(editorContainer, 'selectionchanged', checkNodeChanged);
			on(editorContainer, 'selectionchanged', updateActiveButtons);
			// Custom events to forward
			on(
				editorContainer,
				'selectionchanged valuechanged nodechanged pasteraw paste',
				handleEvent
			);
		};

		/**
		 * Creates the toolbar and appends it to the container
		 * @private
		 */
		initToolBar = function () {
			var	group,
				commands = base.commands,
				exclude  = (options.toolbarExclude || '').split(','),
				groups   = options.toolbar.split('|');

			toolbar = createElement('div', {
				className: 'sceditor-toolbar',
				unselectable: 'on'
			});

			if (options.icons in SCEditor.icons) {
				icons = new SCEditor.icons[options.icons]();
			}

			each(groups, function (_, menuItems) {
				group = createElement('div', {
					className: 'sceditor-group'
				});

				each(menuItems.split(','), function (_, commandName) {
					var	button, shortcut,
						command  = commands[commandName];

					// The commandName must be a valid command and not excluded
					if (!command || exclude.indexOf(commandName) > -1) {
						return;
					}

					shortcut = command.shortcut;
					button   = _tmpl('toolbarButton', {
						name: commandName,
						dispName: base._(command.name ||
								command.tooltip || commandName)
					}, true).firstChild;

					if (icons && icons.create) {
						var icon = icons.create(commandName);
						if (icon) {
							insertBefore(icons.create(commandName),
								button.firstChild);
							addClass(button, 'has-icon');
						}
					}

					button._sceTxtMode = !!command.txtExec;
					button._sceWysiwygMode = !!command.exec;
					toggleClass(button, 'disabled', !command.exec);
					on(button, 'click', function (e) {
						if (!hasClass(button, 'disabled')) {
							handleCommand(button, command);
						}

						updateActiveButtons();
						e.preventDefault();
					});
					// Prevent editor losing focus when button clicked
					on(button, 'mousedown', function (e) {
						base.closeDropDown();
						e.preventDefault();
					});

					if (command.tooltip) {
						attr(button, 'title',
							base._(command.tooltip) +
								(shortcut ? ' (' + shortcut + ')' : '')
						);
					}

					if (shortcut) {
						base.addShortcut(shortcut, commandName);
					}

					if (command.state) {
						btnStateHandlers.push({
							name: commandName,
							state: command.state
						});
					// exec string commands can be passed to queryCommandState
					} else if (isString(command.exec)) {
						btnStateHandlers.push({
							name: commandName,
							state: command.exec
						});
					}

					appendChild(group, button);
					toolbarButtons[commandName] = button;
				});

				// Exclude empty groups
				if (group.firstChild) {
					appendChild(toolbar, group);
				}
			});

			// Append the toolbar to the toolbarContainer option if given
			appendChild(options.toolbarContainer || editorContainer, toolbar);
		};

		/**
		 * Creates the resizer.
		 * @private
		 */
		initResize = function () {
			var	minHeight, maxHeight, minWidth, maxWidth,
				mouseMoveFunc, mouseUpFunc,
				grip        = createElement('div', {
					className: 'sceditor-grip'
				}),
				// Cover is used to cover the editor iframe so document
				// still gets mouse move events
				cover       = createElement('div', {
					className: 'sceditor-resize-cover'
				}),
				moveEvents  = 'touchmove mousemove',
				endEvents   = 'touchcancel touchend mouseup',
				startX      = 0,
				startY      = 0,
				newX        = 0,
				newY        = 0,
				startWidth  = 0,
				startHeight = 0,
				origWidth   = width(editorContainer),
				origHeight  = height(editorContainer),
				isDragging  = false,
				rtl         = base.rtl();

			minHeight = options.resizeMinHeight || origHeight / 1.5;
			maxHeight = options.resizeMaxHeight || origHeight * 2.5;
			minWidth  = options.resizeMinWidth  || origWidth  / 1.25;
			maxWidth  = options.resizeMaxWidth  || origWidth  * 1.25;

			mouseMoveFunc = function (e) {
				// iOS uses window.event
				if (e.type === 'touchmove') {
					e    = globalWin.event;
					newX = e.changedTouches[0].pageX;
					newY = e.changedTouches[0].pageY;
				} else {
					newX = e.pageX;
					newY = e.pageY;
				}

				var	newHeight = startHeight + (newY - startY),
					newWidth  = rtl ?
						startWidth - (newX - startX) :
						startWidth + (newX - startX);

				if (maxWidth > 0 && newWidth > maxWidth) {
					newWidth = maxWidth;
				}
				if (minWidth > 0 && newWidth < minWidth) {
					newWidth = minWidth;
				}
				if (!options.resizeWidth) {
					newWidth = false;
				}

				if (maxHeight > 0 && newHeight > maxHeight) {
					newHeight = maxHeight;
				}
				if (minHeight > 0 && newHeight < minHeight) {
					newHeight = minHeight;
				}
				if (!options.resizeHeight) {
					newHeight = false;
				}

				if (newWidth || newHeight) {
					base.dimensions(newWidth, newHeight);
				}

				e.preventDefault();
			};

			mouseUpFunc = function (e) {
				if (!isDragging) {
					return;
				}

				isDragging = false;

				hide(cover);
				removeClass(editorContainer, 'resizing');
				off(globalDoc, moveEvents, mouseMoveFunc);
				off(globalDoc, endEvents, mouseUpFunc);

				e.preventDefault();
			};

			if (icons && icons.create) {
				var icon = icons.create('grip');
				if (icon) {
					appendChild(grip, icon);
					addClass(grip, 'has-icon');
				}
			}

			appendChild(editorContainer, grip);
			appendChild(editorContainer, cover);
			hide(cover);

			on(grip, 'touchstart mousedown', function (e) {
				// iOS uses window.event
				if (e.type === 'touchstart') {
					e      = globalWin.event;
					startX = e.touches[0].pageX;
					startY = e.touches[0].pageY;
				} else {
					startX = e.pageX;
					startY = e.pageY;
				}

				startWidth  = width(editorContainer);
				startHeight = height(editorContainer);
				isDragging  = true;

				addClass(editorContainer, 'resizing');
				show(cover);
				on(globalDoc, moveEvents, mouseMoveFunc);
				on(globalDoc, endEvents, mouseUpFunc);

				e.preventDefault();
			});
		};

		/**
		 * Prefixes and preloads the emoticon images
		 * @private
		 */
		initEmoticons = function () {
			var	emoticons = options.emoticons;
			var root      = options.emoticonsRoot || '';

			if (emoticons) {
				allEmoticons = extend(
					{}, emoticons.more, emoticons.dropdown, emoticons.hidden
				);
			}

			each(allEmoticons, function (key, url) {
				allEmoticons[key] = _tmpl('emoticon', {
					key: key,
					// Prefix emoticon root to emoticon urls
					url: root + (url.url || url),
					tooltip: url.tooltip || key
				});

				// Preload the emoticon
				if (options.emoticonsEnabled) {
					preLoadCache.push(createElement('img', {
						src: root + (url.url || url)
					}));
				}
			});
		};

		/**
		 * Autofocus the editor
		 * @private
		 */
		autofocus = function (focusEnd) {
			var	range, txtPos,
				node = wysiwygBody.firstChild;

			// Can't focus invisible elements
			if (!isVisible(editorContainer)) {
				return;
			}

			if (base.sourceMode()) {
				txtPos = focusEnd ? sourceEditor.value.length : 0;

				sourceEditor.setSelectionRange(txtPos, txtPos);

				return;
			}

			removeWhiteSpace(wysiwygBody);

			if (focusEnd) {
				if (!(node = wysiwygBody.lastChild)) {
					node = createElement('p', {}, wysiwygDocument);
					appendChild(wysiwygBody, node);
				}

				while (node.lastChild) {
					node = node.lastChild;

					// Should place the cursor before the last <br>
					if (is(node, 'br') && node.previousSibling) {
						node = node.previousSibling;
					}
				}
			}

			range = wysiwygDocument.createRange();

			if (!canHaveChildren(node)) {
				range.setStartBefore(node);

				if (focusEnd) {
					range.setStartAfter(node);
				}
			} else {
				range.selectNodeContents(node);
			}

			range.collapse(!focusEnd);
			rangeHelper.selectRange(range);
			currentSelection = range;

			if (focusEnd) {
				wysiwygBody.scrollTop = wysiwygBody.scrollHeight;
			}

			base.focus();
		};

		/**
		 * Gets if the editor is read only
		 *
		 * @since 1.3.5
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name readOnly
		 * @return {boolean}
		 */
		/**
		 * Sets if the editor is read only
		 *
		 * @param {boolean} readOnly
		 * @since 1.3.5
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name readOnly^2
		 * @return {this}
		 */
		base.readOnly = function (readOnly) {
			if (typeof readOnly !== 'boolean') {
				return !sourceEditor.readonly;
			}

			wysiwygBody.contentEditable = !readOnly;
			sourceEditor.readonly = !readOnly;

			updateToolBar(readOnly);

			return base;
		};

		/**
		 * Gets if the editor is in RTL mode
		 *
		 * @since 1.4.1
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name rtl
		 * @return {boolean}
		 */
		/**
		 * Sets if the editor is in RTL mode
		 *
		 * @param {boolean} rtl
		 * @since 1.4.1
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name rtl^2
		 * @return {this}
		 */
		base.rtl = function (rtl) {
			var dir = rtl ? 'rtl' : 'ltr';

			if (typeof rtl !== 'boolean') {
				return attr(sourceEditor, 'dir') === 'rtl';
			}

			attr(wysiwygBody, 'dir', dir);
			attr(sourceEditor, 'dir', dir);

			removeClass(editorContainer, 'rtl');
			removeClass(editorContainer, 'ltr');
			addClass(editorContainer, dir);

			if (icons && icons.rtl) {
				icons.rtl(rtl);
			}

			return base;
		};

		/**
		 * Updates the toolbar to disable/enable the appropriate buttons
		 * @private
		 */
		updateToolBar = function (disable) {
			var mode = base.inSourceMode() ? '_sceTxtMode' : '_sceWysiwygMode';

			each(toolbarButtons, function (_, button) {
				toggleClass(button, 'disabled', disable || !button[mode]);
			});
		};

		/**
		 * Gets the width of the editor in pixels
		 *
		 * @since 1.3.5
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name width
		 * @return {number}
		 */
		/**
		 * Sets the width of the editor
		 *
		 * @param {number} width Width in pixels
		 * @since 1.3.5
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name width^2
		 * @return {this}
		 */
		/**
		 * Sets the width of the editor
		 *
		 * The saveWidth specifies if to save the width. The stored width can be
		 * used for things like restoring from maximized state.
		 *
		 * @param {number}     width            Width in pixels
		 * @param {boolean}	[saveWidth=true] If to store the width
		 * @since 1.4.1
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name width^3
		 * @return {this}
		 */
		base.width = function (width$1, saveWidth) {
			if (!width$1 && width$1 !== 0) {
				return width(editorContainer);
			}

			base.dimensions(width$1, null, saveWidth);

			return base;
		};

		/**
		 * Returns an object with the properties width and height
		 * which are the width and height of the editor in px.
		 *
		 * @since 1.4.1
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name dimensions
		 * @return {object}
		 */
		/**
		 * <p>Sets the width and/or height of the editor.</p>
		 *
		 * <p>If width or height is not numeric it is ignored.</p>
		 *
		 * @param {number}	width	Width in px
		 * @param {number}	height	Height in px
		 * @since 1.4.1
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name dimensions^2
		 * @return {this}
		 */
		/**
		 * <p>Sets the width and/or height of the editor.</p>
		 *
		 * <p>If width or height is not numeric it is ignored.</p>
		 *
		 * <p>The save argument specifies if to save the new sizes.
		 * The saved sizes can be used for things like restoring from
		 * maximized state. This should normally be left as true.</p>
		 *
		 * @param {number}		width		Width in px
		 * @param {number}		height		Height in px
		 * @param {boolean}	[save=true]	If to store the new sizes
		 * @since 1.4.1
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name dimensions^3
		 * @return {this}
		 */
		base.dimensions = function (width$1, height$1, save) {
			// set undefined width/height to boolean false
			width$1  = (!width$1 && width$1 !== 0) ? false : width$1;
			height$1 = (!height$1 && height$1 !== 0) ? false : height$1;

			if (width$1 === false && height$1 === false) {
				return { width: base.width(), height: base.height() };
			}

			if (width$1 !== false) {
				if (save !== false) {
					options.width = width$1;
				}

				width(editorContainer, width$1);
			}

			if (height$1 !== false) {
				if (save !== false) {
					options.height = height$1;
				}

				height(editorContainer, height$1);
			}

			return base;
		};

		/**
		 * Gets the height of the editor in px
		 *
		 * @since 1.3.5
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name height
		 * @return {number}
		 */
		/**
		 * Sets the height of the editor
		 *
		 * @param {number} height Height in px
		 * @since 1.3.5
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name height^2
		 * @return {this}
		 */
		/**
		 * Sets the height of the editor
		 *
		 * The saveHeight specifies if to save the height.
		 *
		 * The stored height can be used for things like
		 * restoring from maximized state.
		 *
		 * @param {number} height Height in px
		 * @param {boolean} [saveHeight=true] If to store the height
		 * @since 1.4.1
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name height^3
		 * @return {this}
		 */
		base.height = function (height$1, saveHeight) {
			if (!height$1 && height$1 !== 0) {
				return height(editorContainer);
			}

			base.dimensions(null, height$1, saveHeight);

			return base;
		};

		/**
		 * Gets if the editor is maximised or not
		 *
		 * @since 1.4.1
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name maximize
		 * @return {boolean}
		 */
		/**
		 * Sets if the editor is maximised or not
		 *
		 * @param {boolean} maximize If to maximise the editor
		 * @since 1.4.1
		 * @function
		 * @memberOf SCEditor.prototype
		 * @name maximize^2
		 * @return {this}
		 */
		base.maximize = function (maximize) {
			var maximizeSize = 'sceditor-maximize';

			if (isUndefined(maximize)) {
				return hasClass(editorContainer, maximizeSize);
			}

			maximize = !!maximize;

			if (maximize) {
				maximizeScrollPosition = globalWin.pageYOffset;
			}

			toggleClass(globalDoc.documentElement, maximizeSize, maximize);
			toggleClass(globalDoc.body, maximizeSize, maximize);
			toggleClass(editorContainer, maximizeSize, maximize);
			base.width(maximize ? '100%' : options.width, false);
			base.height(maximize ? '100%' : options.height, false);

			if (!maximize) {
				globalWin.scrollTo(0, maximizeScrollPosition);
			}

			autoExpand();

			return base;
		};

		autoExpand = function () {
			if (options.autoExpand && !autoExpandThrottle) {
				autoExpandThrottle = setTimeout(base.expandToContent, 200);
			}
		};

		/**
		 * Expands or shrinks the editors height to the height of it's content
		 *
		 * Unless ignoreMaxHeight is set to true it will not expand
		 * higher than the maxHeight option.
		 *
		 * @since 1.3.5
		 * @param {boolean} [ignoreMaxHeight=false]
		 * @function
		 * @name expandToContent
		 * @memberOf SCEditor.prototype
		 * @see #resizeToContent
		 */
		base.expandToContent = function (ignoreMaxHeight) {
			if (base.maximize()) {
				return;
			}

			clearTimeout(autoExpandThrottle);
			autoExpandThrottle = false;

			if (!autoExpandBounds) {
				var height$1 = options.resizeMinHeight || options.height ||
					height(original);

				autoExpandBounds = {
					min: height$1,
					max: options.resizeMaxHeight || (height$1 * 2)
				};
			}

			var range = globalDoc.createRange();
			range.selectNodeContents(wysiwygBody);

			var rect = range.getBoundingClientRect();
			var current = wysiwygDocument.documentElement.clientHeight - 1;
			var spaceNeeded = rect.bottom - rect.top;
			var newHeight = base.height() + 1 + (spaceNeeded - current);

			if (!ignoreMaxHeight && autoExpandBounds.max !== -1) {
				newHeight = Math.min(newHeight, autoExpandBounds.max);
			}

			base.height(Math.ceil(Math.max(newHeight, autoExpandBounds.min)));
		};

		/**
		 * Destroys the editor, removing all elements and
		 * event handlers.
		 *
		 * Leaves only the original textarea.
		 *
		 * @function
		 * @name destroy
		 * @memberOf SCEditor.prototype
		 */
		base.destroy = function () {
			// Don't destroy if the editor has already been destroyed
			if (!pluginManager) {
				return;
			}

			pluginManager.destroy();

			rangeHelper   = null;
			pluginManager = null;

			if (dropdown) {
				remove(dropdown);
			}

			off(globalDoc, 'click', handleDocumentClick);

			var form = original.form;
			if (form) {
				off(form, 'reset', handleFormReset);
				off(form, 'submit', base.updateOriginal, EVENT_CAPTURE);
			}

			off(window, 'pagehide', base.updateOriginal);
			off(window, 'pageshow', handleFormReset);
			remove(sourceEditor);
			remove(toolbar);
			remove(editorContainer);

			delete original._sceditor;
			show(original);

			original.required = isRequired;
		};


		/**
		 * Creates a menu item drop down
		 *
		 * @param  {HTMLElement} menuItem The button to align the dropdown with
		 * @param  {string} name          Used for styling the dropdown, will be
		 *                                a class sceditor-name
		 * @param  {HTMLElement} content  The HTML content of the dropdown
		 * @function
		 * @name createDropDown
		 * @memberOf SCEditor.prototype
		 */
		base.createDropDown = function (menuItem, name, content) {
			// first click for create second click for close
			var	dropDownCss,
				dropDownClass = 'sceditor-' + name;

			base.closeDropDown();

			// Only close the dropdown if it was already open
			if (dropdown && hasClass(dropdown, dropDownClass)) {
				return;
			}

			dropDownCss = extend({
				top: menuItem.offsetTop,
				left: menuItem.offsetLeft,
				marginTop: menuItem.clientHeight
			}, options.dropDownCss);

			dropdown = createElement('div', {
				className: 'sceditor-dropdown ' + dropDownClass
			});

			css(dropdown, dropDownCss);
			appendChild(dropdown, content);
			appendChild(editorContainer, dropdown);
			on(dropdown, 'click focusin', function (e) {
				// stop clicks within the dropdown from being handled
				e.stopPropagation();
			});

			if (dropdown) {
				var first = find(dropdown, 'input,textarea')[0];
				if (first) {
					first.focus();
				}
			}
		};

		/**
		 * Handles any document click and closes the dropdown if open
		 * @private
		 */
		handleDocumentClick = function (e) {
			// ignore right clicks
			if (e.which !== 3 && dropdown && !e.defaultPrevented) {
				autoUpdate();

				base.closeDropDown();
			}
		};

		/**
		 * Handles the WYSIWYG editors cut & copy events
		 *
		 * By default browsers also copy inherited styling from the stylesheet and
		 * browser default styling which is unnecessary.
		 *
		 * This will ignore inherited styles and only copy inline styling.
		 * @private
		 */
		handleCutCopyEvt = function (e) {
			var range = rangeHelper.selectedRange();
			if (range) {
				var container = createElement('div', {}, wysiwygDocument);
				var firstParent;

				// Copy all inline parent nodes up to the first block parent so can
				// copy inline styles
				var parent = range.commonAncestorContainer;
				while (parent && isInline(parent, true)) {
					if (parent.nodeType === ELEMENT_NODE) {
						var clone = parent.cloneNode();
						if (container.firstChild) {
							appendChild(clone, container.firstChild);
						}

						appendChild(container, clone);
						firstParent = firstParent || clone;
					}
					parent = parent.parentNode;
				}

				appendChild(firstParent || container, range.cloneContents());
				removeWhiteSpace(container);

				e.clipboardData.setData('text/html', container.innerHTML);

				// TODO: Refactor into private shared module with plaintext plugin
				// innerText adds two newlines after <p> tags so convert them to
				// <div> tags
				each(find(container, 'p'), function (_, elm) {
					convertElement(elm, 'div');
				});
				// Remove collapsed <br> tags as innerText converts them to newlines
				each(find(container, 'br'), function (_, elm) {
					if (!elm.nextSibling || !isInline(elm.nextSibling, true)) {
						remove(elm);
					}
				});

				// range.toString() doesn't include newlines so can't use that.
				// selection.toString() seems to use the same method as innerText
				// but needs to be normalised first so using container.innerText
				appendChild(wysiwygBody, container);
				e.clipboardData.setData('text/plain', container.innerText);
				remove(container);

				if (e.type === 'cut') {
					range.deleteContents();
				}

				e.preventDefault();
			}
		};

		/**
		 * Handles the WYSIWYG editors paste event
		 * @private
		 */
		handlePasteEvt = function (e) {
			var editable = wysiwygBody;
			var clipboard = e.clipboardData;
			var loadImage = function (file) {
				var reader = new FileReader();
				reader.onload = function (e) {
					handlePasteData({
						html: '<img src="' + e.target.result + '" />'
					});
				};
				reader.readAsDataURL(file);
			};

			// Modern browsers with clipboard API - everything other than _very_
			// old android web views and UC browser which doesn't support the
			// paste event at all.
			if (clipboard) {
				var data = {};
				var types = clipboard.types;
				var items = clipboard.items;

				e.preventDefault();

				for (var i = 0; i < types.length; i++) {
					// Word sometimes adds copied text as an image so if HTML
					// exists prefer that over images
					if (types.indexOf('text/html') < 0) {
						// Normalise image pasting to paste as a data-uri
						if (globalWin.FileReader && items &&
							IMAGE_MIME_REGEX.test(items[i].type)) {
							return loadImage(clipboard.items[i].getAsFile());
						}
					}

					data[types[i]] = clipboard.getData(types[i]);
				}
				// Call plugins here with file?
				data.text = data['text/plain'];
				data.html = sanitize(data['text/html']);

				handlePasteData(data);
			// If contentsFragment exists then we are already waiting for a
			// previous paste so let the handler for that handle this one too
			} else if (!pasteContentFragment) {
				// Save the scroll position so can be restored
				// when contents is restored
				var scrollTop = editable.scrollTop;

				rangeHelper.saveRange();

				pasteContentFragment = globalDoc.createDocumentFragment();
				while (editable.firstChild) {
					appendChild(pasteContentFragment, editable.firstChild);
				}

				setTimeout(function () {
					var html = editable.innerHTML;

					editable.innerHTML = '';
					appendChild(editable, pasteContentFragment);
					editable.scrollTop = scrollTop;
					pasteContentFragment = false;

					rangeHelper.restoreRange();

					handlePasteData({ html: sanitize(html) });
				}, 0);
			}
		};

		/**
		 * Gets the pasted data, filters it and then inserts it.
		 * @param {Object} data
		 * @private
		 */
		handlePasteData = function (data) {
			var pasteArea = createElement('div', {}, wysiwygDocument);

			pluginManager.call('pasteRaw', data);
			trigger(editorContainer, 'pasteraw', data);

			if (data.html) {
				// Sanitize again in case plugins modified the HTML
				pasteArea.innerHTML = sanitize(data.html);

				// fix any invalid nesting
				fixNesting(pasteArea);
			} else {
				pasteArea.innerHTML = entities(data.text || '');
			}

			var paste = {
				val: pasteArea.innerHTML
			};

			if ('fragmentToSource' in format) {
				paste.val = format
					.fragmentToSource(paste.val, wysiwygDocument, currentNode);
			}

			pluginManager.call('paste', paste);
			trigger(editorContainer, 'paste', paste);

			if ('fragmentToHtml' in format) {
				paste.val = format
					.fragmentToHtml(paste.val, currentNode);
			}

			pluginManager.call('pasteHtml', paste);

			var parent = rangeHelper.getFirstBlockParent();
			base.wysiwygEditorInsertHtml(paste.val, null, true);
			merge(parent);
		};

		/**
		 * Closes any currently open drop down
		 *
		 * @param {boolean} [focus=false] If to focus the editor
		 *                             after closing the drop down
		 * @function
		 * @name closeDropDown
		 * @memberOf SCEditor.prototype
		 */
		base.closeDropDown = function (focus) {
			if (dropdown) {
				remove(dropdown);
				dropdown = null;
			}

			if (focus === true) {
				base.focus();
			}
		};


		/**
		 * Inserts HTML into WYSIWYG editor.
		 *
		 * If endHtml is specified, any selected text will be placed
		 * between html and endHtml. If there is no selected text html
		 * and endHtml will just be concatenate together.
		 *
		 * @param {string} html
		 * @param {string} [endHtml=null]
		 * @param {boolean} [overrideCodeBlocking=false] If to insert the html
		 *                                               into code tags, by
		 *                                               default code tags only
		 *                                               support text.
		 * @function
		 * @name wysiwygEditorInsertHtml
		 * @memberOf SCEditor.prototype
		 */
		base.wysiwygEditorInsertHtml = function (
			html, endHtml, overrideCodeBlocking
		) {
			var	marker, scrollTop, scrollTo,
				editorHeight = height(wysiwygEditor);

			base.focus();

			// TODO: This code tag should be configurable and
			// should maybe convert the HTML into text instead
			// Don't apply to code elements
			if (!overrideCodeBlocking && closest(currentBlockNode, 'code')) {
				return;
			}

			// Insert the HTML and save the range so the editor can be scrolled
			// to the end of the selection. Also allows emoticons to be replaced
			// without affecting the cursor position
			rangeHelper.insertHTML(html, endHtml);
			rangeHelper.saveRange();
			replaceEmoticons();

			// Fix any invalid nesting, e.g. if a quote or other block is inserted
			// into a paragraph
			fixNesting(wysiwygBody);

			// Scroll the editor after the end of the selection
			marker   = find(wysiwygBody, '#sceditor-end-marker')[0];
			show(marker);
			scrollTop = wysiwygBody.scrollTop;
			scrollTo  = (getOffset(marker).top +
				(marker.offsetHeight * 1.5)) - editorHeight;
			hide(marker);

			// Only scroll if marker isn't already visible
			if (scrollTo > scrollTop || scrollTo + editorHeight < scrollTop) {
				wysiwygBody.scrollTop = scrollTo;
			}

			triggerValueChanged(false);
			rangeHelper.restoreRange();

			// Add a new line after the last block element
			// so can always add text after it
			appendNewLine();
		};

		/**
		 * Like wysiwygEditorInsertHtml except it will convert any HTML
		 * into text before inserting it.
		 *
		 * @param {string} text
		 * @param {string} [endText=null]
		 * @function
		 * @name wysiwygEditorInsertText
		 * @memberOf SCEditor.prototype
		 */
		base.wysiwygEditorInsertText = function (text, endText) {
			base.wysiwygEditorInsertHtml(
				entities(text), entities(endText)
			);
		};

		/**
		 * Inserts text into the WYSIWYG or source editor depending on which
		 * mode the editor is in.
		 *
		 * If endText is specified any selected text will be placed between
		 * text and endText. If no text is selected text and endText will
		 * just be concatenate together.
		 *
		 * @param {string} text
		 * @param {string} [endText=null]
		 * @since 1.3.5
		 * @function
		 * @name insertText
		 * @memberOf SCEditor.prototype
		 */
		base.insertText = function (text, endText) {
			if (base.inSourceMode()) {
				base.sourceEditorInsertText(text, endText);
			} else {
				base.wysiwygEditorInsertText(text, endText);
			}

			return base;
		};

		/**
		 * Like wysiwygEditorInsertHtml but inserts text into the
		 * source mode editor instead.
		 *
		 * If endText is specified any selected text will be placed between
		 * text and endText. If no text is selected text and endText will
		 * just be concatenate together.
		 *
		 * The cursor will be placed after the text param. If endText is
		 * specified the cursor will be placed before endText, so passing:<br />
		 *
		 * '[b]', '[/b]'
		 *
		 * Would cause the cursor to be placed:<br />
		 *
		 * [b]Selected text|[/b]
		 *
		 * @param {string} text
		 * @param {string} [endText=null]
		 * @since 1.4.0
		 * @function
		 * @name sourceEditorInsertText
		 * @memberOf SCEditor.prototype
		 */
		base.sourceEditorInsertText = function (text, endText) {
			var scrollTop, currentValue,
				startPos = sourceEditor.selectionStart,
				endPos   = sourceEditor.selectionEnd;

			scrollTop = sourceEditor.scrollTop;
			sourceEditor.focus();
			currentValue = sourceEditor.value;

			if (endText) {
				text += currentValue.substring(startPos, endPos) + endText;
			}

			sourceEditor.value = currentValue.substring(0, startPos) +
				text +
				currentValue.substring(endPos, currentValue.length);

			sourceEditor.selectionStart = (startPos + text.length) -
				(endText ? endText.length : 0);
			sourceEditor.selectionEnd = sourceEditor.selectionStart;

			sourceEditor.scrollTop = scrollTop;
			sourceEditor.focus();

			triggerValueChanged();
		};

		/**
		 * Gets the current instance of the rangeHelper class
		 * for the editor.
		 *
		 * @return {RangeHelper}
		 * @function
		 * @name getRangeHelper
		 * @memberOf SCEditor.prototype
		 */
		base.getRangeHelper = function () {
			return rangeHelper;
		};

		/**
		 * Gets or sets the source editor caret position.
		 *
		 * @param {Object} [position]
		 * @return {this}
		 * @function
		 * @since 1.4.5
		 * @name sourceEditorCaret
		 * @memberOf SCEditor.prototype
		 */
		base.sourceEditorCaret = function (position) {
			sourceEditor.focus();

			if (position) {
				sourceEditor.selectionStart = position.start;
				sourceEditor.selectionEnd = position.end;

				return this;
			}

			return {
				start: sourceEditor.selectionStart,
				end: sourceEditor.selectionEnd
			};
		};

		/**
		 * Gets the value of the editor.
		 *
		 * If the editor is in WYSIWYG mode it will return the filtered
		 * HTML from it (converted to BBCode if using the BBCode plugin).
		 * It it's in Source Mode it will return the unfiltered contents
		 * of the source editor (if using the BBCode plugin this will be
		 * BBCode again).
		 *
		 * @since 1.3.5
		 * @return {string}
		 * @function
		 * @name val
		 * @memberOf SCEditor.prototype
		 */
		/**
		 * Sets the value of the editor.
		 *
		 * If filter set true the val will be passed through the filter
		 * function. If using the BBCode plugin it will pass the val to
		 * the BBCode filter to convert any BBCode into HTML.
		 *
		 * @param {string} val
		 * @param {boolean} [filter=true]
		 * @return {this}
		 * @since 1.3.5
		 * @function
		 * @name val^2
		 * @memberOf SCEditor.prototype
		 */
		base.val = function (val, filter) {
			if (!isString(val)) {
				return base.inSourceMode() ?
					base.getSourceEditorValue(false) :
					base.getWysiwygEditorValue(filter);
			}

			if (!base.inSourceMode()) {
				if (filter !== false && 'toHtml' in format) {
					val = format.toHtml(val);
				}

				base.setWysiwygEditorValue(val);
			} else {
				base.setSourceEditorValue(val);
			}

			return base;
		};

		/**
		 * Inserts HTML/BBCode into the editor
		 *
		 * If end is supplied any selected text will be placed between
		 * start and end. If there is no selected text start and end
		 * will be concatenate together.
		 *
		 * If the filter param is set to true, the HTML/BBCode will be
		 * passed through any plugin filters. If using the BBCode plugin
		 * this will convert any BBCode into HTML.
		 *
		 * @param {string} start
		 * @param {string} [end=null]
		 * @param {boolean} [filter=true]
		 * @param {boolean} [convertEmoticons=true] If to convert emoticons
		 * @return {this}
		 * @since 1.3.5
		 * @function
		 * @name insert
		 * @memberOf SCEditor.prototype
		 */
		/**
		 * Inserts HTML/BBCode into the editor
		 *
		 * If end is supplied any selected text will be placed between
		 * start and end. If there is no selected text start and end
		 * will be concatenate together.
		 *
		 * If the filter param is set to true, the HTML/BBCode will be
		 * passed through any plugin filters. If using the BBCode plugin
		 * this will convert any BBCode into HTML.
		 *
		 * If the allowMixed param is set to true, HTML any will not be
		 * escaped
		 *
		 * @param {string} start
		 * @param {string} [end=null]
		 * @param {boolean} [filter=true]
		 * @param {boolean} [convertEmoticons=true] If to convert emoticons
		 * @param {boolean} [allowMixed=false]
		 * @return {this}
		 * @since 1.4.3
		 * @function
		 * @name insert^2
		 * @memberOf SCEditor.prototype
		 */
		// eslint-disable-next-line max-params
		base.insert = function (
			start, end, filter, convertEmoticons, allowMixed
		) {
			if (base.inSourceMode()) {
				base.sourceEditorInsertText(start, end);
				return base;
			}

			// Add the selection between start and end
			if (end) {
				var	html = rangeHelper.selectedHtml();

				if (filter !== false && 'fragmentToSource' in format) {
					html = format
						.fragmentToSource(html, wysiwygDocument, currentNode);
				}

				start += html + end;
			}
			// TODO: This filter should allow empty tags as it's inserting.
			if (filter !== false && 'fragmentToHtml' in format) {
				start = format.fragmentToHtml(start, currentNode);
			}

			// Convert any escaped HTML back into HTML if mixed is allowed
			if (filter !== false && allowMixed === true) {
				start = start.replace(/&lt;/g, '<')
					.replace(/&gt;/g, '>')
					.replace(/&amp;/g, '&');
			}

			base.wysiwygEditorInsertHtml(start);

			return base;
		};

		/**
		 * Gets the WYSIWYG editors HTML value.
		 *
		 * If using a plugin that filters the Ht Ml like the BBCode plugin
		 * it will return the result of the filtering (BBCode) unless the
		 * filter param is set to false.
		 *
		 * @param {boolean} [filter=true]
		 * @return {string}
		 * @function
		 * @name getWysiwygEditorValue
		 * @memberOf SCEditor.prototype
		 */
		base.getWysiwygEditorValue = function (filter) {
			var	html;
			// Create a tmp node to store contents so it can be modified
			// without affecting anything else.
			var tmp = createElement('div', {}, wysiwygDocument);
			var childNodes = wysiwygBody.childNodes;

			for (var i = 0; i < childNodes.length; i++) {
				appendChild(tmp, childNodes[i].cloneNode(true));
			}

			appendChild(wysiwygBody, tmp);
			fixNesting(tmp);
			remove(tmp);

			html = tmp.innerHTML;

			// filter the HTML and DOM through any plugins
			if (filter !== false && format.hasOwnProperty('toSource')) {
				html = format.toSource(html, wysiwygDocument);
			}

			return html;
		};

		/**
		 * Gets the WYSIWYG editor's iFrame Body.
		 *
		 * @return {HTMLElement}
		 * @function
		 * @since 1.4.3
		 * @name getBody
		 * @memberOf SCEditor.prototype
		 */
		base.getBody = function () {
			return wysiwygBody;
		};

		/**
		 * Gets the WYSIWYG editors container area (whole iFrame).
		 *
		 * @return {HTMLElement}
		 * @function
		 * @since 1.4.3
		 * @name getContentAreaContainer
		 * @memberOf SCEditor.prototype
		 */
		base.getContentAreaContainer = function () {
			return wysiwygEditor;
		};

		/**
		 * Gets the text editor value
		 *
		 * If using a plugin that filters the text like the BBCode plugin
		 * it will return the result of the filtering which is BBCode to
		 * HTML so it will return HTML. If filter is set to false it will
		 * just return the contents of the source editor (BBCode).
		 *
		 * @param {boolean} [filter=true]
		 * @return {string}
		 * @function
		 * @since 1.4.0
		 * @name getSourceEditorValue
		 * @memberOf SCEditor.prototype
		 */
		base.getSourceEditorValue = function (filter) {
			var val = sourceEditor.value;

			if (filter !== false && 'toHtml' in format) {
				val = format.toHtml(val);
			}

			return val;
		};

		/**
		 * Sets the WYSIWYG HTML editor value. Should only be the HTML
		 * contained within the body tags
		 *
		 * @param {string} value
		 * @function
		 * @name setWysiwygEditorValue
		 * @memberOf SCEditor.prototype
		 */
		base.setWysiwygEditorValue = function (value) {
			if (!value) {
				value = '<p><br /></p>';
			}

			wysiwygBody.innerHTML = sanitize(value);
			replaceEmoticons();

			appendNewLine();
			triggerValueChanged();
			autoExpand();
		};

		/**
		 * Sets the text editor value
		 *
		 * @param {string} value
		 * @function
		 * @name setSourceEditorValue
		 * @memberOf SCEditor.prototype
		 */
		base.setSourceEditorValue = function (value) {
			sourceEditor.value = value;

			triggerValueChanged();
		};

		/**
		 * Updates the textarea that the editor is replacing
		 * with the value currently inside the editor.
		 *
		 * @function
		 * @name updateOriginal
		 * @since 1.4.0
		 * @memberOf SCEditor.prototype
		 */
		base.updateOriginal = function () {
			original.value = base.val();
		};

		/**
		 * Replaces any emoticon codes in the passed HTML
		 * with their emoticon images
		 * @private
		 */
		replaceEmoticons = function () {
			if (options.emoticonsEnabled) {
				replace(wysiwygBody, allEmoticons, options.emoticonsCompat);
			}
		};

		/**
		 * If the editor is in source code mode
		 *
		 * @return {boolean}
		 * @function
		 * @name inSourceMode
		 * @memberOf SCEditor.prototype
		 */
		base.inSourceMode = function () {
			return hasClass(editorContainer, 'sourceMode');
		};

		/**
		 * Gets if the editor is in sourceMode
		 *
		 * @return boolean
		 * @function
		 * @name sourceMode
		 * @memberOf SCEditor.prototype
		 */
		/**
		 * Sets if the editor is in sourceMode
		 *
		 * @param {boolean} enable
		 * @return {this}
		 * @function
		 * @name sourceMode^2
		 * @memberOf SCEditor.prototype
		 */
		base.sourceMode = function (enable) {
			var inSourceMode = base.inSourceMode();

			if (typeof enable !== 'boolean') {
				return inSourceMode;
			}

			if ((inSourceMode && !enable) || (!inSourceMode && enable)) {
				base.toggleSourceMode();
			}

			return base;
		};

		/**
		 * Switches between the WYSIWYG and source modes
		 *
		 * @function
		 * @name toggleSourceMode
		 * @since 1.4.0
		 * @memberOf SCEditor.prototype
		 */
		base.toggleSourceMode = function () {
			var isInSourceMode = base.inSourceMode();

			// don't allow switching to WYSIWYG if doesn't support it
			if (!isWysiwygSupported && isInSourceMode) {
				return;
			}

			if (!isInSourceMode) {
				rangeHelper.saveRange();
				rangeHelper.clear();
			}

			currentSelection = null;
			base.blur();

			if (isInSourceMode) {
				base.setWysiwygEditorValue(base.getSourceEditorValue());
			} else {
				base.setSourceEditorValue(base.getWysiwygEditorValue());
			}

			toggle(sourceEditor);
			toggle(wysiwygEditor);

			toggleClass(editorContainer, 'wysiwygMode', isInSourceMode);
			toggleClass(editorContainer, 'sourceMode', !isInSourceMode);

			updateToolBar();
			updateActiveButtons();
		};

		/**
		 * Gets the selected text of the source editor
		 * @return {string}
		 * @private
		 */
		sourceEditorSelectedText = function () {
			sourceEditor.focus();

			return sourceEditor.value.substring(
				sourceEditor.selectionStart,
				sourceEditor.selectionEnd
			);
		};

		/**
		 * Handles the passed command
		 * @private
		 */
		handleCommand = function (caller, cmd) {
			// check if in text mode and handle text commands
			if (base.inSourceMode()) {
				if (cmd.txtExec) {
					if (Array.isArray(cmd.txtExec)) {
						base.sourceEditorInsertText.apply(base, cmd.txtExec);
					} else {
						cmd.txtExec.call(base, caller, sourceEditorSelectedText());
					}
				}
			} else if (cmd.exec) {
				if (isFunction(cmd.exec)) {
					cmd.exec.call(base, caller);
				} else {
					base.execCommand(
						cmd.exec,
						cmd.hasOwnProperty('execParam') ? cmd.execParam : null
					);
				}
			}

		};

		/**
		 * Executes a command on the WYSIWYG editor
		 *
		 * @param {string} command
		 * @param {String|Boolean} [param]
		 * @function
		 * @name execCommand
		 * @memberOf SCEditor.prototype
		 */
		base.execCommand = function (command, param) {
			var	executed    = false,
				commandObj  = base.commands[command];

			base.focus();

			// TODO: make configurable
			// don't apply any commands to code elements
			if (closest(rangeHelper.parentNode(), 'code')) {
				return;
			}

			try {
				executed = wysiwygDocument.execCommand(command, false, param);
			} catch (ex) { }

			// show error if execution failed and an error message exists
			if (!executed && commandObj && commandObj.errorMessage) {
				/*global alert:false*/
				alert(base._(commandObj.errorMessage));
			}

			updateActiveButtons();
		};

		/**
		 * Checks if the current selection has changed and triggers
		 * the selectionchanged event if it has.
		 *
		 * In browsers other that don't support selectionchange event it will check
		 * at most once every 100ms.
		 * @private
		 */
		checkSelectionChanged = function () {
			function check() {
				// Don't create new selection if there isn't one (like after
				// blur event in iOS)
				if (wysiwygWindow.getSelection() &&
					wysiwygWindow.getSelection().rangeCount <= 0) {
					currentSelection = null;
				// rangeHelper could be null if editor was destroyed
				// before the timeout had finished
				} else if (rangeHelper && !rangeHelper.compare(currentSelection)) {
					currentSelection = rangeHelper.cloneSelected();

					// If the selection is in an inline wrap it in a block.
					// Fixes #331
					if (currentSelection && currentSelection.collapsed) {
						var parent = currentSelection.startContainer;
						var offset = currentSelection.startOffset;

						// Handle if selection is placed before/after an element
						if (offset && parent.nodeType !== TEXT_NODE) {
							parent = parent.childNodes[offset];
						}

						while (parent && parent.parentNode !== wysiwygBody) {
							parent = parent.parentNode;
						}

						if (parent && isInline(parent, true)) {
							rangeHelper.saveRange();
							wrapInlines(wysiwygBody, wysiwygDocument);
							rangeHelper.restoreRange();
						}
					}

					trigger(editorContainer, 'selectionchanged');
				}

				isSelectionCheckPending = false;
			}

			if (isSelectionCheckPending) {
				return;
			}

			isSelectionCheckPending = true;

			// Don't need to limit checking if browser supports the Selection API
			if ('onselectionchange' in wysiwygDocument) {
				check();
			} else {
				setTimeout(check, 100);
			}
		};

		/**
		 * Checks if the current node has changed and triggers
		 * the nodechanged event if it has
		 * @private
		 */
		checkNodeChanged = function () {
			// check if node has changed
			var	oldNode,
				node = rangeHelper.parentNode();

			if (currentNode !== node) {
				oldNode          = currentNode;
				currentNode      = node;
				currentBlockNode = rangeHelper.getFirstBlockParent(node);

				trigger(editorContainer, 'nodechanged', {
					oldNode: oldNode,
					newNode: currentNode
				});
			}
		};

		/**
		 * Gets the current node that contains the selection/caret in
		 * WYSIWYG mode.
		 *
		 * Will be null in sourceMode or if there is no selection.
		 *
		 * @return {?Node}
		 * @function
		 * @name currentNode
		 * @memberOf SCEditor.prototype
		 */
		base.currentNode = function () {
			return currentNode;
		};

		/**
		 * Gets the first block level node that contains the
		 * selection/caret in WYSIWYG mode.
		 *
		 * Will be null in sourceMode or if there is no selection.
		 *
		 * @return {?Node}
		 * @function
		 * @name currentBlockNode
		 * @memberOf SCEditor.prototype
		 * @since 1.4.4
		 */
		base.currentBlockNode = function () {
			return currentBlockNode;
		};

		/**
		 * Updates if buttons are active or not
		 * @private
		 */
		updateActiveButtons = function () {
			var firstBlock, parent;
			var activeClass = 'active';
			var doc         = wysiwygDocument;
			var isSource    = base.sourceMode();

			if (base.readOnly()) {
				each(find(toolbar, activeClass), function (_, menuItem) {
					removeClass(menuItem, activeClass);
				});
				return;
			}

			if (!isSource) {
				parent     = rangeHelper.parentNode();
				firstBlock = rangeHelper.getFirstBlockParent(parent);
			}

			for (var j = 0; j < btnStateHandlers.length; j++) {
				var state      = 0;
				var btn        = toolbarButtons[btnStateHandlers[j].name];
				var stateFn    = btnStateHandlers[j].state;
				var isDisabled = (isSource && !btn._sceTxtMode) ||
							(!isSource && !btn._sceWysiwygMode);

				if (isString(stateFn)) {
					if (!isSource) {
						try {
							state = doc.queryCommandEnabled(stateFn) ? 0 : -1;

							// eslint-disable-next-line max-depth
							if (state > -1) {
								state = doc.queryCommandState(stateFn) ? 1 : 0;
							}
						} catch (ex) {}
					}
				} else if (!isDisabled) {
					state = stateFn.call(base, parent, firstBlock);
				}

				toggleClass(btn, 'disabled', isDisabled || state < 0);
				toggleClass(btn, activeClass, state > 0);
			}

			if (icons && icons.update) {
				icons.update(isSource, parent, firstBlock);
			}
		};

		/**
		 * Handles any key press in the WYSIWYG editor
		 *
		 * @private
		 */
		handleKeyPress = function (e) {
			// FF bug: https://bugzilla.mozilla.org/show_bug.cgi?id=501496
			if (e.defaultPrevented) {
				return;
			}

			base.closeDropDown();

			// 13 = enter key
			if (e.which === 13) {
				var LIST_TAGS = 'li,ul,ol';

				// "Fix" (cludge) for blocklevel elements being duplicated in some
				// browsers when enter is pressed instead of inserting a newline
				if (!is(currentBlockNode, LIST_TAGS) &&
					hasStyling(currentBlockNode)) {

					var br = createElement('br', {}, wysiwygDocument);
					rangeHelper.insertNode(br);

					// Last <br> of a block will be collapsed  so need to make sure
					// the <br> that was inserted isn't the last node of a block.
					var parent  = br.parentNode;
					var lastChild = parent.lastChild;

					// Sometimes an empty next node is created after the <br>
					if (lastChild && lastChild.nodeType === TEXT_NODE &&
						lastChild.nodeValue === '') {
						remove(lastChild);
						lastChild = parent.lastChild;
					}

					// If this is the last BR of a block and the previous
					// sibling is inline then will need an extra BR. This
					// is needed because the last BR of a block will be
					// collapsed. Fixes issue #248
					if (!isInline(parent, true) && lastChild === br &&
						isInline(br.previousSibling)) {
						rangeHelper.insertHTML('<br>');
					}

					e.preventDefault();
				}
			}
		};

		/**
		 * Makes sure that if there is a code or quote tag at the
		 * end of the editor, that there is a new line after it.
		 *
		 * If there wasn't a new line at the end you wouldn't be able
		 * to enter any text after a code/quote tag
		 * @return {void}
		 * @private
		 */
		appendNewLine = function () {
			// Check all nodes in reverse until either add a new line
			// or reach a non-empty textnode or BR at which point can
			// stop checking.
			rTraverse(wysiwygBody, function (node) {
				// Last block, add new line after if has styling
				if (node.nodeType === ELEMENT_NODE &&
					!/inline/.test(css(node, 'display'))) {

					// Add line break after if has styling
					if (!is(node, '.sceditor-nlf') && hasStyling(node)) {
						var paragraph = createElement('p', {}, wysiwygDocument);
						paragraph.className = 'sceditor-nlf';
						paragraph.innerHTML = '<br />';
						appendChild(wysiwygBody, paragraph);
						return false;
					}
				}

				// Last non-empty text node or line break.
				// No need to add line-break after them
				if ((node.nodeType === 3 && !/^\s*$/.test(node.nodeValue)) ||
					is(node, 'br')) {
					return false;
				}
			});
		};

		/**
		 * Handles form reset event
		 * @private
		 */
		handleFormReset = function () {
			base.val(original.value);
		};

		/**
		 * Handles any mousedown press in the WYSIWYG editor
		 * @private
		 */
		handleMouseDown = function () {
			base.closeDropDown();
		};

		/**
		 * Translates the string into the locale language.
		 *
		 * Replaces any {0}, {1}, {2}, ect. with the params provided.
		 *
		 * @param {string} str
		 * @param {...String} args
		 * @return {string}
		 * @function
		 * @name _
		 * @memberOf SCEditor.prototype
		 */
		base._ = function () {
			var	undef,
				args = arguments;

			if (locale && locale[args[0]]) {
				args[0] = locale[args[0]];
			}

			return args[0].replace(/\{(\d+)\}/g, function (str, p1) {
				return args[p1 - 0 + 1] !== undef ?
					args[p1 - 0 + 1] :
					'{' + p1 + '}';
			});
		};

		/**
		 * Passes events on to any handlers
		 * @private
		 * @return void
		 */
		handleEvent = function (e) {
			if (pluginManager) {
				// Send event to all plugins
				pluginManager.call(e.type + 'Event', e, base);
			}

			// convert the event into a custom event to send
			var name = (e.target === sourceEditor ? 'scesrc' : 'scewys') + e.type;

			if (eventHandlers[name]) {
				eventHandlers[name].forEach(function (fn) {
					fn.call(base, e);
				});
			}
		};

		/**
		 * Binds a handler to the specified events
		 *
		 * This function only binds to a limited list of
		 * supported events.
		 *
		 * The supported events are:
		 *
		 * * keyup
		 * * keydown
		 * * Keypress
		 * * blur
		 * * focus
		 * * input
		 * * nodechanged - When the current node containing
		 * 		the selection changes in WYSIWYG mode
		 * * contextmenu
		 * * selectionchanged
		 * * valuechanged
		 *
		 *
		 * The events param should be a string containing the event(s)
		 * to bind this handler to. If multiple, they should be separated
		 * by spaces.
		 *
		 * @param  {string} events
		 * @param  {Function} handler
		 * @param  {boolean} excludeWysiwyg If to exclude adding this handler
		 *                                  to the WYSIWYG editor
		 * @param  {boolean} excludeSource  if to exclude adding this handler
		 *                                  to the source editor
		 * @return {this}
		 * @function
		 * @name bind
		 * @memberOf SCEditor.prototype
		 * @since 1.4.1
		 */
		base.bind = function (events, handler, excludeWysiwyg, excludeSource) {
			events = events.split(' ');

			var i  = events.length;
			while (i--) {
				if (isFunction(handler)) {
					var wysEvent = 'scewys' + events[i];
					var srcEvent = 'scesrc' + events[i];
					// Use custom events to allow passing the instance as the
					// 2nd argument.
					// Also allows unbinding without unbinding the editors own
					// event handlers.
					if (!excludeWysiwyg) {
						eventHandlers[wysEvent] = eventHandlers[wysEvent] || [];
						eventHandlers[wysEvent].push(handler);
					}

					if (!excludeSource) {
						eventHandlers[srcEvent] = eventHandlers[srcEvent] || [];
						eventHandlers[srcEvent].push(handler);
					}

					// Start sending value changed events
					if (events[i] === 'valuechanged') {
						triggerValueChanged.hasHandler = true;
					}
				}
			}

			return base;
		};

		/**
		 * Unbinds an event that was bound using bind().
		 *
		 * @param  {string} events
		 * @param  {Function} handler
		 * @param  {boolean} excludeWysiwyg If to exclude unbinding this
		 *                                  handler from the WYSIWYG editor
		 * @param  {boolean} excludeSource  if to exclude unbinding this
		 *                                  handler from the source editor
		 * @return {this}
		 * @function
		 * @name unbind
		 * @memberOf SCEditor.prototype
		 * @since 1.4.1
		 * @see bind
		 */
		base.unbind = function (events, handler, excludeWysiwyg, excludeSource) {
			events = events.split(' ');

			var i  = events.length;
			while (i--) {
				if (isFunction(handler)) {
					if (!excludeWysiwyg) {
						arrayRemove(
							eventHandlers['scewys' + events[i]] || [], handler);
					}

					if (!excludeSource) {
						arrayRemove(
							eventHandlers['scesrc' + events[i]] || [], handler);
					}
				}
			}

			return base;
		};

		/**
		 * Blurs the editors input area
		 *
		 * @return {this}
		 * @function
		 * @name blur
		 * @memberOf SCEditor.prototype
		 * @since 1.3.6
		 */
		/**
		 * Adds a handler to the editors blur event
		 *
		 * @param  {Function} handler
		 * @param  {boolean} excludeWysiwyg If to exclude adding this handler
		 *                                  to the WYSIWYG editor
		 * @param  {boolean} excludeSource  if to exclude adding this handler
		 *                                  to the source editor
		 * @return {this}
		 * @function
		 * @name blur^2
		 * @memberOf SCEditor.prototype
		 * @since 1.4.1
		 */
		base.blur = function (handler, excludeWysiwyg, excludeSource) {
			if (isFunction(handler)) {
				base.bind('blur', handler, excludeWysiwyg, excludeSource);
			} else if (!base.sourceMode()) {
				wysiwygBody.blur();
			} else {
				sourceEditor.blur();
			}

			return base;
		};

		/**
		 * Focuses the editors input area
		 *
		 * @return {this}
		 * @function
		 * @name focus
		 * @memberOf SCEditor.prototype
		 */
		/**
		 * Adds an event handler to the focus event
		 *
		 * @param  {Function} handler
		 * @param  {boolean} excludeWysiwyg If to exclude adding this handler
		 *                                  to the WYSIWYG editor
		 * @param  {boolean} excludeSource  if to exclude adding this handler
		 *                                  to the source editor
		 * @return {this}
		 * @function
		 * @name focus^2
		 * @memberOf SCEditor.prototype
		 * @since 1.4.1
		 */
		base.focus = function (handler, excludeWysiwyg, excludeSource) {
			if (isFunction(handler)) {
				base.bind('focus', handler, excludeWysiwyg, excludeSource);
			} else if (!base.inSourceMode()) {
				// Already has focus so do nothing
				if (find(wysiwygDocument, ':focus').length) {
					return;
				}

				var container;
				var rng = rangeHelper.selectedRange();

				// Fix FF bug where it shows the cursor in the wrong place
				// if the editor hasn't had focus before. See issue #393
				if (!currentSelection) {
					autofocus(true);
				}

				// Check if cursor is set after a BR when the BR is the only
				// child of the parent. In Firefox this causes a line break
				// to occur when something is typed. See issue #321
				if (rng && rng.endOffset === 1 && rng.collapsed) {
					container = rng.endContainer;

					if (container && container.childNodes.length === 1 &&
						is(container.firstChild, 'br')) {
						rng.setStartBefore(container.firstChild);
						rng.collapse(true);
						rangeHelper.selectRange(rng);
					}
				}

				wysiwygWindow.focus();
				wysiwygBody.focus();
			} else {
				sourceEditor.focus();
			}

			updateActiveButtons();

			return base;
		};

		/**
		 * Adds a handler to the key down event
		 *
		 * @param  {Function} handler
		 * @param  {boolean} excludeWysiwyg If to exclude adding this handler
		 *                                  to the WYSIWYG editor
		 * @param  {boolean} excludeSource  If to exclude adding this handler
		 *                                  to the source editor
		 * @return {this}
		 * @function
		 * @name keyDown
		 * @memberOf SCEditor.prototype
		 * @since 1.4.1
		 */
		base.keyDown = function (handler, excludeWysiwyg, excludeSource) {
			return base.bind('keydown', handler, excludeWysiwyg, excludeSource);
		};

		/**
		 * Adds a handler to the key press event
		 *
		 * @param  {Function} handler
		 * @param  {boolean} excludeWysiwyg If to exclude adding this handler
		 *                                  to the WYSIWYG editor
		 * @param  {boolean} excludeSource  If to exclude adding this handler
		 *                                  to the source editor
		 * @return {this}
		 * @function
		 * @name keyPress
		 * @memberOf SCEditor.prototype
		 * @since 1.4.1
		 */
		base.keyPress = function (handler, excludeWysiwyg, excludeSource) {
			return base
				.bind('keypress', handler, excludeWysiwyg, excludeSource);
		};

		/**
		 * Adds a handler to the key up event
		 *
		 * @param  {Function} handler
		 * @param  {boolean} excludeWysiwyg If to exclude adding this handler
		 *                                  to the WYSIWYG editor
		 * @param  {boolean} excludeSource  If to exclude adding this handler
		 *                                  to the source editor
		 * @return {this}
		 * @function
		 * @name keyUp
		 * @memberOf SCEditor.prototype
		 * @since 1.4.1
		 */
		base.keyUp = function (handler, excludeWysiwyg, excludeSource) {
			return base.bind('keyup', handler, excludeWysiwyg, excludeSource);
		};

		/**
		 * Adds a handler to the node changed event.
		 *
		 * Happens whenever the node containing the selection/caret
		 * changes in WYSIWYG mode.
		 *
		 * @param  {Function} handler
		 * @return {this}
		 * @function
		 * @name nodeChanged
		 * @memberOf SCEditor.prototype
		 * @since 1.4.1
		 */
		base.nodeChanged = function (handler) {
			return base.bind('nodechanged', handler, false, true);
		};

		/**
		 * Adds a handler to the selection changed event
		 *
		 * Happens whenever the selection changes in WYSIWYG mode.
		 *
		 * @param  {Function} handler
		 * @return {this}
		 * @function
		 * @name selectionChanged
		 * @memberOf SCEditor.prototype
		 * @since 1.4.1
		 */
		base.selectionChanged = function (handler) {
			return base.bind('selectionchanged', handler, false, true);
		};

		/**
		 * Adds a handler to the value changed event
		 *
		 * Happens whenever the current editor value changes.
		 *
		 * Whenever anything is inserted, the value changed or
		 * 1.5 secs after text is typed. If a space is typed it will
		 * cause the event to be triggered immediately instead of
		 * after 1.5 seconds
		 *
		 * @param  {Function} handler
		 * @param  {boolean} excludeWysiwyg If to exclude adding this handler
		 *                                  to the WYSIWYG editor
		 * @param  {boolean} excludeSource  If to exclude adding this handler
		 *                                  to the source editor
		 * @return {this}
		 * @function
		 * @name valueChanged
		 * @memberOf SCEditor.prototype
		 * @since 1.4.5
		 */
		base.valueChanged = function (handler, excludeWysiwyg, excludeSource) {
			return base
				.bind('valuechanged', handler, excludeWysiwyg, excludeSource);
		};

		/**
		 * Emoticons keypress handler
		 * @private
		 */
		emoticonsKeyPress = function (e) {
			var	replacedEmoticon,
				cachePos       = 0,
				emoticonsCache = base.emoticonsCache,
				curChar        = String.fromCharCode(e.which);

			// TODO: Make configurable
			if (closest(currentBlockNode, 'code')) {
				return;
			}

			if (!emoticonsCache) {
				emoticonsCache = [];

				each(allEmoticons, function (key, html) {
					emoticonsCache[cachePos++] = [key, html];
				});

				emoticonsCache.sort(function (a, b) {
					return a[0].length - b[0].length;
				});

				base.emoticonsCache = emoticonsCache;
				base.longestEmoticonCode =
					emoticonsCache[emoticonsCache.length - 1][0].length;
			}

			replacedEmoticon = rangeHelper.replaceKeyword(
				base.emoticonsCache,
				true,
				true,
				base.longestEmoticonCode,
				options.emoticonsCompat,
				curChar
			);

			if (replacedEmoticon) {
				if (!options.emoticonsCompat || !/^\s$/.test(curChar)) {
					e.preventDefault();
				}
			}
		};

		/**
		 * Makes sure emoticons are surrounded by whitespace
		 * @private
		 */
		emoticonsCheckWhitespace = function () {
			checkWhitespace(currentBlockNode, rangeHelper);
		};

		/**
		 * Gets if emoticons are currently enabled
		 * @return {boolean}
		 * @function
		 * @name emoticons
		 * @memberOf SCEditor.prototype
		 * @since 1.4.2
		 */
		/**
		 * Enables/disables emoticons
		 *
		 * @param {boolean} enable
		 * @return {this}
		 * @function
		 * @name emoticons^2
		 * @memberOf SCEditor.prototype
		 * @since 1.4.2
		 */
		base.emoticons = function (enable) {
			if (!enable && enable !== false) {
				return options.emoticonsEnabled;
			}

			options.emoticonsEnabled = enable;

			if (enable) {
				on(wysiwygBody, 'keypress', emoticonsKeyPress);

				if (!base.sourceMode()) {
					rangeHelper.saveRange();

					replaceEmoticons();
					triggerValueChanged(false);

					rangeHelper.restoreRange();
				}
			} else {
				var emoticons =
					find(wysiwygBody, 'img[data-sceditor-emoticon]');

				each(emoticons, function (_, img) {
					var text = data(img, 'sceditor-emoticon');
					var textNode = wysiwygDocument.createTextNode(text);
					img.parentNode.replaceChild(textNode, img);
				});

				off(wysiwygBody, 'keypress', emoticonsKeyPress);

				triggerValueChanged();
			}

			return base;
		};

		/**
		 * Gets the current WYSIWYG editors inline CSS
		 *
		 * @return {string}
		 * @function
		 * @name css
		 * @memberOf SCEditor.prototype
		 * @since 1.4.3
		 */
		/**
		 * Sets inline CSS for the WYSIWYG editor
		 *
		 * @param {string} css
		 * @return {this}
		 * @function
		 * @name css^2
		 * @memberOf SCEditor.prototype
		 * @since 1.4.3
		 */
		base.css = function (css) {
			if (!inlineCss) {
				inlineCss = createElement('style', {
					id: 'inline'
				}, wysiwygDocument);

				appendChild(wysiwygDocument.head, inlineCss);
			}

			if (!isString(css)) {
				return inlineCss.styleSheet ?
					inlineCss.styleSheet.cssText : inlineCss.innerHTML;
			}

			if (inlineCss.styleSheet) {
				inlineCss.styleSheet.cssText = css;
			} else {
				inlineCss.innerHTML = css;
			}

			return base;
		};

		/**
		 * Handles the keydown event, used for shortcuts
		 * @private
		 */
		handleKeyDown = function (e) {
			var	shortcut   = [],
				SHIFT_KEYS = {
					'`': '~',
					'1': '!',
					'2': '@',
					'3': '#',
					'4': '$',
					'5': '%',
					'6': '^',
					'7': '&',
					'8': '*',
					'9': '(',
					'0': ')',
					'-': '_',
					'=': '+',
					';': ': ',
					'\'': '"',
					',': '<',
					'.': '>',
					'/': '?',
					'\\': '|',
					'[': '{',
					']': '}'
				},
				SPECIAL_KEYS = {
					8: 'backspace',
					9: 'tab',
					13: 'enter',
					19: 'pause',
					20: 'capslock',
					27: 'esc',
					32: 'space',
					33: 'pageup',
					34: 'pagedown',
					35: 'end',
					36: 'home',
					37: 'left',
					38: 'up',
					39: 'right',
					40: 'down',
					45: 'insert',
					46: 'del',
					91: 'win',
					92: 'win',
					93: 'select',
					96: '0',
					97: '1',
					98: '2',
					99: '3',
					100: '4',
					101: '5',
					102: '6',
					103: '7',
					104: '8',
					105: '9',
					106: '*',
					107: '+',
					109: '-',
					110: '.',
					111: '/',
					112: 'f1',
					113: 'f2',
					114: 'f3',
					115: 'f4',
					116: 'f5',
					117: 'f6',
					118: 'f7',
					119: 'f8',
					120: 'f9',
					121: 'f10',
					122: 'f11',
					123: 'f12',
					144: 'numlock',
					145: 'scrolllock',
					186: ';',
					187: '=',
					188: ',',
					189: '-',
					190: '.',
					191: '/',
					192: '`',
					219: '[',
					220: '\\',
					221: ']',
					222: '\''
				},
				NUMPAD_SHIFT_KEYS = {
					109: '-',
					110: 'del',
					111: '/',
					96: '0',
					97: '1',
					98: '2',
					99: '3',
					100: '4',
					101: '5',
					102: '6',
					103: '7',
					104: '8',
					105: '9'
				},
				which     = e.which,
				character = SPECIAL_KEYS[which] ||
					String.fromCharCode(which).toLowerCase();

			if (e.ctrlKey || e.metaKey) {
				shortcut.push('ctrl');
			}

			if (e.altKey) {
				shortcut.push('alt');
			}

			if (e.shiftKey) {
				shortcut.push('shift');

				if (NUMPAD_SHIFT_KEYS[which]) {
					character = NUMPAD_SHIFT_KEYS[which];
				} else if (SHIFT_KEYS[character]) {
					character = SHIFT_KEYS[character];
				}
			}

			// Shift is 16, ctrl is 17 and alt is 18
			if (character && (which < 16 || which > 18)) {
				shortcut.push(character);
			}

			shortcut = shortcut.join('+');
			if (shortcutHandlers[shortcut] &&
				shortcutHandlers[shortcut].call(base) === false) {

				e.stopPropagation();
				e.preventDefault();
			}
		};

		/**
		 * Adds a shortcut handler to the editor
		 * @param  {string}          shortcut
		 * @param  {String|Function} cmd
		 * @return {sceditor}
		 */
		base.addShortcut = function (shortcut, cmd) {
			shortcut = shortcut.toLowerCase();

			if (isString(cmd)) {
				shortcutHandlers[shortcut] = function () {
					handleCommand(toolbarButtons[cmd], base.commands[cmd]);

					return false;
				};
			} else {
				shortcutHandlers[shortcut] = cmd;
			}

			return base;
		};

		/**
		 * Removes a shortcut handler
		 * @param  {string} shortcut
		 * @return {sceditor}
		 */
		base.removeShortcut = function (shortcut) {
			delete shortcutHandlers[shortcut.toLowerCase()];

			return base;
		};

		/**
		 * Handles the backspace key press
		 *
		 * Will remove block styling like quotes/code ect if at the start.
		 * @private
		 */
		handleBackSpace = function (e) {
			var	node, offset, range, parent;

			// 8 is the backspace key
			if (options.disableBlockRemove || e.which !== 8 ||
				!(range = rangeHelper.selectedRange())) {
				return;
			}

			node   = range.startContainer;
			offset = range.startOffset;

			if (offset !== 0 || !(parent = currentStyledBlockNode()) ||
				is(parent, 'body')) {
				return;
			}

			while (node !== parent) {
				while (node.previousSibling) {
					node = node.previousSibling;

					// Everything but empty text nodes before the cursor
					// should prevent the style from being removed
					if (node.nodeType !== TEXT_NODE || node.nodeValue) {
						return;
					}
				}

				if (!(node = node.parentNode)) {
					return;
				}
			}

			// The backspace was pressed at the start of
			// the container so clear the style
			base.clearBlockFormatting(parent);
			e.preventDefault();
		};

		/**
		 * Gets the first styled block node that contains the cursor
		 * @return {HTMLElement}
		 */
		currentStyledBlockNode = function () {
			var block = currentBlockNode;

			while (!hasStyling(block) || isInline(block, true)) {
				if (!(block = block.parentNode) || is(block, 'body')) {
					return;
				}
			}

			return block;
		};

		/**
		 * Clears the formatting of the passed block element.
		 *
		 * If block is false, if will clear the styling of the first
		 * block level element that contains the cursor.
		 * @param  {HTMLElement} block
		 * @since 1.4.4
		 */
		base.clearBlockFormatting = function (block) {
			block = block || currentStyledBlockNode();

			if (!block || is(block, 'body')) {
				return base;
			}

			rangeHelper.saveRange();

			block.className = '';

			attr(block, 'style', '');

			if (!is(block, 'p,div,td')) {
				convertElement(block, 'p');
			}

			rangeHelper.restoreRange();
			return base;
		};

		/**
		 * Triggers the valueChanged signal if there is
		 * a plugin that handles it.
		 *
		 * If rangeHelper.saveRange() has already been
		 * called, then saveRange should be set to false
		 * to prevent the range being saved twice.
		 *
		 * @since 1.4.5
		 * @param {boolean} saveRange If to call rangeHelper.saveRange().
		 * @private
		 */
		triggerValueChanged = function (saveRange) {
			if (!pluginManager ||
				(!pluginManager.hasHandler('valuechangedEvent') &&
					!triggerValueChanged.hasHandler)) {
				return;
			}

			var	currentHtml,
				sourceMode   = base.sourceMode(),
				hasSelection = !sourceMode && rangeHelper.hasSelection();

			// Composition end isn't guaranteed to fire but must have
			// ended when triggerValueChanged() is called so reset it
			isComposing = false;

			// Don't need to save the range if sceditor-start-marker
			// is present as the range is already saved
			saveRange = saveRange !== false &&
				!wysiwygDocument.getElementById('sceditor-start-marker');

			// Clear any current timeout as it's now been triggered
			if (valueChangedKeyUpTimer) {
				clearTimeout(valueChangedKeyUpTimer);
				valueChangedKeyUpTimer = false;
			}

			if (hasSelection && saveRange) {
				rangeHelper.saveRange();
			}

			currentHtml = sourceMode ? sourceEditor.value : wysiwygBody.innerHTML;

			// Only trigger if something has actually changed.
			if (currentHtml !== triggerValueChanged.lastVal) {
				triggerValueChanged.lastVal = currentHtml;

				trigger(editorContainer, 'valuechanged', {
					rawValue: sourceMode ? base.val() : currentHtml
				});
			}

			if (hasSelection && saveRange) {
				rangeHelper.removeMarkers();
			}
		};

		/**
		 * Should be called whenever there is a blur event
		 * @private
		 */
		valueChangedBlur = function () {
			if (valueChangedKeyUpTimer) {
				triggerValueChanged();
			}
		};

		/**
		 * Should be called whenever there is a keypress event
		 * @param  {Event} e The keypress event
		 * @private
		 */
		valueChangedKeyUp = function (e) {
			var which         = e.which,
				lastChar      = valueChangedKeyUp.lastChar,
				lastWasSpace  = (lastChar === 13 || lastChar === 32),
				lastWasDelete = (lastChar === 8 || lastChar === 46);

			valueChangedKeyUp.lastChar = which;

			if (isComposing) {
				return;
			}

			// 13 = return & 32 = space
			if (which === 13 || which === 32) {
				if (!lastWasSpace) {
					triggerValueChanged();
				} else {
					valueChangedKeyUp.triggerNext = true;
				}
			// 8 = backspace & 46 = del
			} else if (which === 8 || which === 46) {
				if (!lastWasDelete) {
					triggerValueChanged();
				} else {
					valueChangedKeyUp.triggerNext = true;
				}
			} else if (valueChangedKeyUp.triggerNext) {
				triggerValueChanged();
				valueChangedKeyUp.triggerNext = false;
			}

			// Clear the previous timeout and set a new one.
			clearTimeout(valueChangedKeyUpTimer);

			// Trigger the event 1.5s after the last keypress if space
			// isn't pressed. This might need to be lowered, will need
			// to look into what the slowest average Chars Per Min is.
			valueChangedKeyUpTimer = setTimeout(function () {
				if (!isComposing) {
					triggerValueChanged();
				}
			}, 1500);
		};

		handleComposition = function (e) {
			isComposing = /start/i.test(e.type);

			if (!isComposing) {
				triggerValueChanged();
			}
		};

		autoUpdate = function () {
			base.updateOriginal();
		};

		// run the initializer
		init();
	}

	/**
	 * Map containing the loaded SCEditor locales
	 * @type {Object}
	 * @name locale
	 * @memberOf sceditor
	 */
	SCEditor.locale = {};

	SCEditor.formats = {};
	SCEditor.icons = {};


	/**
	 * Static command helper class
	 * @class command
	 * @name sceditor.command
	 */
	SCEditor.command =
	/** @lends sceditor.command */
	{
		/**
		 * Gets a command
		 *
		 * @param {string} name
		 * @return {Object|null}
		 * @since v1.3.5
		 */
		get: function (name) {
			return defaultCmds[name] || null;
		},

		/**
		 * <p>Adds a command to the editor or updates an existing
		 * command if a command with the specified name already exists.</p>
		 *
		 * <p>Once a command is add it can be included in the toolbar by
		 * adding it's name to the toolbar option in the constructor. It
		 * can also be executed manually by calling
		 * {@link sceditor.execCommand}</p>
		 *
		 * @example
		 * SCEditor.command.set("hello",
		 * {
		 *     exec: function () {
		 *         alert("Hello World!");
		 *     }
		 * });
		 *
		 * @param {string} name
		 * @param {Object} cmd
		 * @return {this|false} Returns false if name or cmd is false
		 * @since v1.3.5
		 */
		set: function (name, cmd) {
			if (!name || !cmd) {
				return false;
			}

			// merge any existing command properties
			cmd = extend(defaultCmds[name] || {}, cmd);

			cmd.remove = function () {
				SCEditor.command.remove(name);
			};

			defaultCmds[name] = cmd;
			return this;
		},

		/**
		 * Removes a command
		 *
		 * @param {string} name
		 * @return {this}
		 * @since v1.3.5
		 */
		remove: function (name) {
			if (defaultCmds[name]) {
				delete defaultCmds[name];
			}

			return this;
		}
	};

	/**
	 * SCEditor
	 * http://www.sceditor.com/
	 *
	 * Copyright (C) 2017, Sam Clarke (samclarke.com)
	 *
	 * SCEditor is licensed under the MIT license:
	 *	http://www.opensource.org/licenses/mit-license.php
	 *
	 * @fileoverview SCEditor - A lightweight WYSIWYG BBCode and HTML editor
	 * @author Sam Clarke
	 */


	window.sceditor = {
		command: SCEditor.command,
		commands: defaultCmds,
		defaultOptions: defaultOptions,

		ios: ios,
		isWysiwygSupported: isWysiwygSupported,

		regexEscape: regex,
		escapeEntities: entities,
		escapeUriScheme: uriScheme,

		dom: {
			css: css,
			attr: attr,
			removeAttr: removeAttr,
			is: is,
			closest: closest,
			width: width,
			height: height,
			traverse: traverse,
			rTraverse: rTraverse,
			parseHTML: parseHTML,
			hasStyling: hasStyling,
			convertElement: convertElement,
			blockLevelList: blockLevelList,
			canHaveChildren: canHaveChildren,
			isInline: isInline,
			copyCSS: copyCSS,
			fixNesting: fixNesting,
			findCommonAncestor: findCommonAncestor,
			getSibling: getSibling,
			removeWhiteSpace: removeWhiteSpace,
			extractContents: extractContents,
			getOffset: getOffset,
			getStyle: getStyle,
			hasStyle: hasStyle
		},
		locale: SCEditor.locale,
		icons: SCEditor.icons,
		utils: {
			each: each,
			isEmptyObject: isEmptyObject,
			extend: extend
		},
		plugins: PluginManager.plugins,
		formats: SCEditor.formats,
		create: function (textarea, options) {
			options = options || {};

			// Don't allow the editor to be initialised
			// on it's own source editor
			if (parent(textarea, '.sceditor-container')) {
				return;
			}

			if (options.runWithoutWysiwygSupport || isWysiwygSupported) {
				/*eslint no-new: off*/
				(new SCEditor(textarea, options));
			}
		},
		instance: function (textarea) {
			return textarea._sceditor;
		}
	};

}());
