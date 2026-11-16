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
	 * Kunena change : variable for active image control
	 */
	var selected = [];  //=> selected must be global

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
				// eslint-disable-next-line no-unused-vars
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

		// Should only merge inline tags and should not merge <br> tags
		if (!isInline(node) || tagName === 'BR') {
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
		dropDownCss: { },

		/**
		 * An array of tags that are allowed in the editor content.
		 * If a tag is not listed here, it will be removed when the content is
		 * sanitized.
		 *
		 * 1 Tag is already added by default: ['iframe']. No need to add this
		 * further.
		 *
		 * @type {Array}
		 */
		allowedTags: [],

		/**
		 * An array of attributes that are allowed on tags in the editor content.
		 * If an attribute is not listed here, it will be removed when the content
		 * is sanitized.
		 *
		 * 3 Attributes are already added by default:
		 * 	['allowfullscreen', 'frameborder', 'target'].
		 * No need to add these further.
		 *
		 * @type {Array}
		 */
		allowedAttributes: []
	};

	// Regex used by DOMPurify to filter URLs. Might as well match here as otherwise
	// URLs will be filtered out by DOMPurify anyway
	var VALID_URI_REGEX = /^(?:(?:(?:f|ht)tps?|mailto|tel|callto|sms|cid|xmpp|matrix):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i;
	// Safe image data URIs, Kunena change on VALID_DATA_REGEX 
	var VALID_DATA_REGEX = /^data:image\/(a?png|bmp|gif|p?jpe?g|webp|avif);/i;
	var WHITESPACE_REGEX = /[\u0000-\u0020\u00A0\u1680\u180E\u2000-\u2029\u205F\u3000]/g;

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
	 * ftps
	 * ftp
	 * mailto
	 * tel
	 * callto
	 * sms
	 * cid
	 * xmpp
	 * matrix
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
			location = window.location;

		// Match previous behaviour for empty or data: URIs
		if (!url || VALID_DATA_REGEX.test(url)) {
			return url;
		}

		// Invalid scheme so make relative
		if (!VALID_URI_REGEX.test(url.replace(WHITESPACE_REGEX, ''))) {
			path = location.pathname.split('/');
			path.pop();

			url = location.protocol + '//' +
				location.host +
				path.join('/') + '/' +
				url;

			if (!VALID_URI_REGEX.test(url.replace(WHITESPACE_REGEX, ''))) {
				return '';
			}
		}

		return url;
	}

	/*! @license DOMPurify 3.4.16 | (c) Cure53 and other contributors | Released under the Apache license 2.0 and Mozilla Public License 2.0 | github.com/cure53/DOMPurify/blob/3.4.16/LICENSE */
	function _OverloadYield(e, d) {
		this.v = e, this.k = d;
	}
	function _arrayLikeToArray(r, a) {
		(null == a || a > r.length) && (a = r.length);
		for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e];
		return n;
	}
	function _arrayWithHoles(r) {
		if (Array.isArray(r)) return r;
	}
	function _iterableToArrayLimit(r, l) {
		var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"];
		if (null != t) {
			var e, n, i, u, a = [], f = !0, o = !1;
			try {
				if (i = (t = t.call(r)).next, 0 === l) {
					if (Object(t) !== t) return;
					f = !1;
				} else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0);
			} catch (r) {
				o = !0, n = r;
			} finally {
				try {
					if (!f && null != t.return && (u = t.return(), Object(u) !== u)) return;
				} finally {
					if (o) throw n;
				}
			}
			return a;
		}
	}
	function _nonIterableRest() {
		throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}
	/*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */
	function _slicedToArray(r, e) {
		return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest();
	}
	function _unsupportedIterableToArray(r, a) {
		if (r) {
			if ("string" == typeof r) return _arrayLikeToArray(r, a);
			var t = {}.toString.call(r).slice(8, -1);
			return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0;
		}
	}
	function AsyncGenerator(e) {
		var t, n;
		function resume(t, n) {
			try {
				var r = e[t](n), o = r.value, u = o instanceof _OverloadYield;
				Promise.resolve(u ? o.v : o).then(function(n) {
					if (u) {
						var i = "return" === t && o.k ? t : "next";
						if (!o.k || n.done) return resume(i, n);
						n = e[i](n).value;
					}
					settle(!!r.done, n);
				}, function(e) {
					resume("throw", e);
				});
			} catch (e) {
				settle(2, e);
			}
		}
		function settle(e, r) {
			2 === e ? t.reject(r) : t.resolve({
				value: r,
				done: e
			}), (t = t.next) ? resume(t.key, t.arg) : n = null;
		}
		this._invoke = function(e, r) {
			return new Promise(function(o, u) {
				var i = {
					key: e,
					arg: r,
					resolve: o,
					reject: u,
					next: null
				};
				n ? n = n.next = i : (t = n = i, resume(e, r));
			});
		}, "function" != typeof e.return && (this.return = void 0);
	}
	AsyncGenerator.prototype["function" == typeof Symbol && Symbol.asyncIterator || "@@asyncIterator"] = function() {
		return this;
	}, AsyncGenerator.prototype.next = function(e) {
		return this._invoke("next", e);
	}, AsyncGenerator.prototype.throw = function(e) {
		return this._invoke("throw", e);
	}, AsyncGenerator.prototype.return = function(e) {
		return this._invoke("return", e);
	};
	const entries = Object.entries;
	const setPrototypeOf = Object.setPrototypeOf;
	const isFrozen = Object.isFrozen;
	const getPrototypeOf = Object.getPrototypeOf;
	const getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;
	let freeze = Object.freeze;
	let seal = Object.seal;
	let create = Object.create;
	let _ref = typeof Reflect !== "undefined" && Reflect;
	let apply = _ref.apply;
	let construct = _ref.construct;
	if (!freeze) freeze = function freeze(x) {
		return x;
	};
	if (!seal) seal = function seal(x) {
		return x;
	};
	if (!apply) apply = function apply(func, thisArg) {
		for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) args[_key - 2] = arguments[_key];
		return func.apply(thisArg, args);
	};
	if (!construct) construct = function construct(Func) {
		for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) args[_key2 - 1] = arguments[_key2];
		return new Func(...args);
	};
	const arrayForEach = unapply(Array.prototype.forEach);
	Array.prototype.indexOf;
	const arrayLastIndexOf = unapply(Array.prototype.lastIndexOf);
	const arrayPop = unapply(Array.prototype.pop);
	const arrayPush = unapply(Array.prototype.push);
	Array.prototype.slice;
	const arraySplice = unapply(Array.prototype.splice);
	const arrayIsArray = Array.isArray;
	const stringToLowerCase = unapply(String.prototype.toLowerCase);
	const stringToString = unapply(String.prototype.toString);
	const stringMatch = unapply(String.prototype.match);
	const stringReplace = unapply(String.prototype.replace);
	const stringIndexOf = unapply(String.prototype.indexOf);
	const stringTrim = unapply(String.prototype.trim);
	const numberToString = unapply(Number.prototype.toString);
	const booleanToString = unapply(Boolean.prototype.toString);
	const bigintToString = typeof BigInt === "undefined" ? null : unapply(BigInt.prototype.toString);
	const symbolToString = typeof Symbol === "undefined" ? null : unapply(Symbol.prototype.toString);
	const objectHasOwnProperty = unapply(Object.prototype.hasOwnProperty);
	const objectToString = unapply(Object.prototype.toString);
	const regExpTest = unapply(RegExp.prototype.test);
	const typeErrorCreate = unconstruct(TypeError);
	/**
	* Creates a new function that calls the given function with a specified thisArg and arguments.
	*
	* @param func - The function to be wrapped and called.
	* @returns A new function that calls the given function with a specified thisArg and arguments.
	*/
	function unapply(func) {
		return function(thisArg) {
			if (thisArg instanceof RegExp) thisArg.lastIndex = 0;
			for (var _len3 = arguments.length, args = new Array(_len3 > 1 ? _len3 - 1 : 0), _key3 = 1; _key3 < _len3; _key3++) args[_key3 - 1] = arguments[_key3];
			return apply(func, thisArg, args);
		};
	}
	/**
	* Creates a new function that constructs an instance of the given constructor function with the provided arguments.
	*
	* @param func - The constructor function to be wrapped and called.
	* @returns A new function that constructs an instance of the given constructor function with the provided arguments.
	*/
	function unconstruct(Func) {
		return function() {
			for (var _len4 = arguments.length, args = new Array(_len4), _key4 = 0; _key4 < _len4; _key4++) args[_key4] = arguments[_key4];
			return construct(Func, args);
		};
	}
	/**
	* Add properties to a lookup table
	*
	* @param set - The set to which elements will be added.
	* @param array - The array containing elements to be added to the set.
	* @param transformCaseFunc - An optional function to transform the case of each element before adding to the set.
	* @returns The modified set with added elements.
	*/
	function addToSet(set, array) {
		let transformCaseFunc = arguments.length > 2 && arguments[2] !== void 0 ? arguments[2] : stringToLowerCase;
		if (setPrototypeOf) setPrototypeOf(set, null);
		if (!arrayIsArray(array)) return set;
		let l = array.length;
		while (l--) {
			let element = array[l];
			if (typeof element === "string") {
				const lcElement = transformCaseFunc(element);
				if (lcElement !== element) {
					if (!isFrozen(array)) array[l] = lcElement;
					element = lcElement;
				}
			}
			set[element] = true;
		}
		return set;
	}
	/**
	* Clean up an array to harden against CSPP
	*
	* @param array - The array to be cleaned.
	* @returns The cleaned version of the array
	*/
	function cleanArray(array) {
		for (let index = 0; index < array.length; index++) if (!objectHasOwnProperty(array, index)) array[index] = null;
		return array;
	}
	/**
	* Shallow clone an object
	*
	* @param object - The object to be cloned.
	* @returns A new object that copies the original.
	*/
	function clone(object) {
		const newObject = create(null);
		for (const _ref2 of entries(object)) {
			var _ref3 = _slicedToArray(_ref2, 2);
			const property = _ref3[0];
			const value = _ref3[1];
			if (objectHasOwnProperty(object, property)) {
				if (arrayIsArray(value)) newObject[property] = cleanArray(value);
				else if (value && typeof value === "object" && value.constructor === Object) newObject[property] = clone(value);
				else newObject[property] = value;
			}
		}
		return newObject;
	}
	/**
	* Convert non-node values into strings without depending on direct property access.
	*
	* @param value - The value to stringify.
	* @returns A string representation of the provided value.
	*/
	function stringifyValue(value) {
		switch (typeof value) {
			case "string": return value;
			case "number": return numberToString(value);
			case "boolean": return booleanToString(value);
			case "bigint": return bigintToString ? bigintToString(value) : "0";
			case "symbol": return symbolToString ? symbolToString(value) : "Symbol()";
			case "undefined": return objectToString(value);
			case "function":
			case "object": {
				if (value === null) return objectToString(value);
				const valueAsRecord = value;
				const valueToString = lookupGetter(valueAsRecord, "toString");
				if (typeof valueToString === "function") {
					const stringified = valueToString(valueAsRecord);
					return typeof stringified === "string" ? stringified : objectToString(stringified);
				}
				return objectToString(value);
			}
			default: return objectToString(value);
		}
	}
	/**
	* This method automatically checks if the prop is function or getter and behaves accordingly.
	*
	* @param object - The object to look up the getter function in its prototype chain.
	* @param prop - The property name for which to find the getter function.
	* @returns The getter function found in the prototype chain or a fallback function.
	*/
	function lookupGetter(object, prop) {
		while (object !== null) {
			const desc = getOwnPropertyDescriptor(object, prop);
			if (desc) {
				if (desc.get) return unapply(desc.get);
				if (typeof desc.value === "function") return unapply(desc.value);
			}
			object = getPrototypeOf(object);
		}
		function fallbackValue() {
			return null;
		}
		return fallbackValue;
	}
	function isRegex(value) {
		try {
			regExpTest(value, "");
			return true;
		} catch (_unused) {
			return false;
		}
	}
	const html$1 = freeze([
		"a",
		"abbr",
		"acronym",
		"address",
		"area",
		"article",
		"aside",
		"audio",
		"b",
		"bdi",
		"bdo",
		"big",
		"blink",
		"blockquote",
		"body",
		"br",
		"button",
		"canvas",
		"caption",
		"center",
		"cite",
		"code",
		"col",
		"colgroup",
		"content",
		"data",
		"datalist",
		"dd",
		"decorator",
		"del",
		"details",
		"dfn",
		"dialog",
		"dir",
		"div",
		"dl",
		"dt",
		"element",
		"em",
		"fieldset",
		"figcaption",
		"figure",
		"font",
		"footer",
		"form",
		"h1",
		"h2",
		"h3",
		"h4",
		"h5",
		"h6",
		"head",
		"header",
		"hgroup",
		"hr",
		"html",
		"i",
		"img",
		"input",
		"ins",
		"kbd",
		"label",
		"legend",
		"li",
		"main",
		"map",
		"mark",
		"marquee",
		"menu",
		"menuitem",
		"meter",
		"nav",
		"nobr",
		"ol",
		"optgroup",
		"option",
		"output",
		"p",
		"picture",
		"pre",
		"progress",
		"q",
		"rp",
		"rt",
		"ruby",
		"s",
		"samp",
		"search",
		"section",
		"select",
		"shadow",
		"slot",
		"small",
		"source",
		"spacer",
		"span",
		"strike",
		"strong",
		"style",
		"sub",
		"summary",
		"sup",
		"table",
		"tbody",
		"td",
		"template",
		"textarea",
		"tfoot",
		"th",
		"thead",
		"time",
		"tr",
		"track",
		"tt",
		"u",
		"ul",
		"var",
		"video",
		"wbr"
	]);
	const svg$1 = freeze([
		"svg",
		"a",
		"altglyph",
		"altglyphdef",
		"altglyphitem",
		"animatecolor",
		"animatemotion",
		"animatetransform",
		"circle",
		"clippath",
		"defs",
		"desc",
		"ellipse",
		"enterkeyhint",
		"exportparts",
		"filter",
		"font",
		"g",
		"glyph",
		"glyphref",
		"hkern",
		"image",
		"inputmode",
		"line",
		"lineargradient",
		"marker",
		"mask",
		"metadata",
		"mpath",
		"part",
		"path",
		"pattern",
		"polygon",
		"polyline",
		"radialgradient",
		"rect",
		"stop",
		"style",
		"switch",
		"symbol",
		"text",
		"textpath",
		"title",
		"tref",
		"tspan",
		"view",
		"vkern"
	]);
	const svgFilters = freeze([
		"feBlend",
		"feColorMatrix",
		"feComponentTransfer",
		"feComposite",
		"feConvolveMatrix",
		"feDiffuseLighting",
		"feDisplacementMap",
		"feDistantLight",
		"feDropShadow",
		"feFlood",
		"feFuncA",
		"feFuncB",
		"feFuncG",
		"feFuncR",
		"feGaussianBlur",
		"feImage",
		"feMerge",
		"feMergeNode",
		"feMorphology",
		"feOffset",
		"fePointLight",
		"feSpecularLighting",
		"feSpotLight",
		"feTile",
		"feTurbulence"
	]);
	const svgDisallowed = freeze([
		"animate",
		"color-profile",
		"cursor",
		"discard",
		"font-face",
		"font-face-format",
		"font-face-name",
		"font-face-src",
		"font-face-uri",
		"foreignobject",
		"hatch",
		"hatchpath",
		"mesh",
		"meshgradient",
		"meshpatch",
		"meshrow",
		"missing-glyph",
		"script",
		"set",
		"solidcolor",
		"unknown",
		"use"
	]);
	const mathMl$1 = freeze([
		"math",
		"menclose",
		"merror",
		"mfenced",
		"mfrac",
		"mglyph",
		"mi",
		"mlabeledtr",
		"mmultiscripts",
		"mn",
		"mo",
		"mover",
		"mpadded",
		"mphantom",
		"mroot",
		"mrow",
		"ms",
		"mspace",
		"msqrt",
		"mstyle",
		"msub",
		"msup",
		"msubsup",
		"mtable",
		"mtd",
		"mtext",
		"mtr",
		"munder",
		"munderover",
		"mprescripts"
	]);
	const mathMlDisallowed = freeze([
		"maction",
		"maligngroup",
		"malignmark",
		"mlongdiv",
		"mscarries",
		"mscarry",
		"msgroup",
		"mstack",
		"msline",
		"msrow",
		"semantics",
		"annotation",
		"annotation-xml",
		"mprescripts",
		"none"
	]);
	const text = freeze(["#text"]);
	const html = freeze([
		"accept",
		"action",
		"align",
		"alt",
		"autocapitalize",
		"autocomplete",
		"autopictureinpicture",
		"autoplay",
		"background",
		"bgcolor",
		"border",
		"capture",
		"cellpadding",
		"cellspacing",
		"checked",
		"cite",
		"class",
		"clear",
		"color",
		"cols",
		"colspan",
		"command",
		"commandfor",
		"controls",
		"controlslist",
		"coords",
		"crossorigin",
		"datetime",
		"decoding",
		"default",
		"dir",
		"disabled",
		"disablepictureinpicture",
		"disableremoteplayback",
		"download",
		"draggable",
		"enctype",
		"enterkeyhint",
		"exportparts",
		"face",
		"for",
		"headers",
		"height",
		"hidden",
		"high",
		"href",
		"hreflang",
		"id",
		"inert",
		"inputmode",
		"integrity",
		"ismap",
		"kind",
		"label",
		"lang",
		"list",
		"loading",
		"loop",
		"low",
		"max",
		"maxlength",
		"media",
		"method",
		"min",
		"minlength",
		"multiple",
		"muted",
		"name",
		"nonce",
		"noshade",
		"novalidate",
		"nowrap",
		"open",
		"optimum",
		"part",
		"pattern",
		"placeholder",
		"playsinline",
		"popover",
		"popovertarget",
		"popovertargetaction",
		"poster",
		"preload",
		"pubdate",
		"radiogroup",
		"readonly",
		"rel",
		"required",
		"rev",
		"reversed",
		"role",
		"rows",
		"rowspan",
		"spellcheck",
		"scope",
		"selected",
		"shape",
		"size",
		"sizes",
		"slot",
		"span",
		"srclang",
		"start",
		"src",
		"srcset",
		"step",
		"style",
		"summary",
		"tabindex",
		"title",
		"translate",
		"type",
		"usemap",
		"valign",
		"value",
		"width",
		"wrap",
		"xmlns"
	]);
	const svg = freeze([
		"accent-height",
		"accumulate",
		"additive",
		"alignment-baseline",
		"amplitude",
		"ascent",
		"attributename",
		"attributetype",
		"azimuth",
		"basefrequency",
		"baseline-shift",
		"begin",
		"bias",
		"by",
		"class",
		"clip",
		"clippathunits",
		"clip-path",
		"clip-rule",
		"color",
		"color-interpolation",
		"color-interpolation-filters",
		"color-profile",
		"color-rendering",
		"cx",
		"cy",
		"d",
		"dx",
		"dy",
		"diffuseconstant",
		"direction",
		"display",
		"divisor",
		"dominant-baseline",
		"dur",
		"edgemode",
		"elevation",
		"end",
		"exponent",
		"fill",
		"fill-opacity",
		"fill-rule",
		"filter",
		"filterunits",
		"flood-color",
		"flood-opacity",
		"font-family",
		"font-size",
		"font-size-adjust",
		"font-stretch",
		"font-style",
		"font-variant",
		"font-weight",
		"fx",
		"fy",
		"g1",
		"g2",
		"glyph-name",
		"glyphref",
		"gradientunits",
		"gradienttransform",
		"height",
		"href",
		"id",
		"image-rendering",
		"in",
		"in2",
		"intercept",
		"k",
		"k1",
		"k2",
		"k3",
		"k4",
		"kerning",
		"keypoints",
		"keysplines",
		"keytimes",
		"lang",
		"lengthadjust",
		"letter-spacing",
		"kernelmatrix",
		"kernelunitlength",
		"lighting-color",
		"local",
		"marker-end",
		"marker-mid",
		"marker-start",
		"markerheight",
		"markerunits",
		"markerwidth",
		"maskcontentunits",
		"maskunits",
		"max",
		"mask",
		"mask-type",
		"media",
		"method",
		"mode",
		"min",
		"name",
		"numoctaves",
		"offset",
		"operator",
		"opacity",
		"order",
		"orient",
		"orientation",
		"origin",
		"overflow",
		"paint-order",
		"path",
		"pathlength",
		"patterncontentunits",
		"patterntransform",
		"patternunits",
		"pointer-events",
		"points",
		"preservealpha",
		"preserveaspectratio",
		"primitiveunits",
		"r",
		"rx",
		"ry",
		"radius",
		"refx",
		"refy",
		"repeatcount",
		"repeatdur",
		"restart",
		"result",
		"rotate",
		"scale",
		"seed",
		"shape-rendering",
		"slope",
		"specularconstant",
		"specularexponent",
		"spreadmethod",
		"startoffset",
		"stddeviation",
		"stitchtiles",
		"stop-color",
		"stop-opacity",
		"stroke-dasharray",
		"stroke-dashoffset",
		"stroke-linecap",
		"stroke-linejoin",
		"stroke-miterlimit",
		"stroke-opacity",
		"stroke",
		"stroke-width",
		"style",
		"surfacescale",
		"systemlanguage",
		"tabindex",
		"tablevalues",
		"targetx",
		"targety",
		"transform",
		"transform-origin",
		"text-anchor",
		"text-decoration",
		"text-orientation",
		"text-rendering",
		"textlength",
		"type",
		"u1",
		"u2",
		"unicode",
		"values",
		"vector-effect",
		"viewbox",
		"visibility",
		"version",
		"vert-adv-y",
		"vert-origin-x",
		"vert-origin-y",
		"width",
		"word-spacing",
		"wrap",
		"writing-mode",
		"xchannelselector",
		"ychannelselector",
		"x",
		"x1",
		"x2",
		"xmlns",
		"y",
		"y1",
		"y2",
		"z",
		"zoomandpan"
	]);
	const mathMl = freeze([
		"accent",
		"accentunder",
		"align",
		"bevelled",
		"close",
		"columnalign",
		"columnlines",
		"columnspacing",
		"columnspan",
		"denomalign",
		"depth",
		"dir",
		"display",
		"displaystyle",
		"encoding",
		"fence",
		"frame",
		"height",
		"href",
		"id",
		"largeop",
		"length",
		"linethickness",
		"lquote",
		"lspace",
		"mathbackground",
		"mathcolor",
		"mathsize",
		"mathvariant",
		"maxsize",
		"minsize",
		"movablelimits",
		"notation",
		"numalign",
		"open",
		"rowalign",
		"rowlines",
		"rowspacing",
		"rowspan",
		"rspace",
		"rquote",
		"scriptlevel",
		"scriptminsize",
		"scriptsizemultiplier",
		"selection",
		"separator",
		"separators",
		"stretchy",
		"subscriptshift",
		"supscriptshift",
		"symmetric",
		"voffset",
		"width",
		"xmlns"
	]);
	const xml = freeze([
		"xlink:href",
		"xml:id",
		"xlink:title",
		"xml:space",
		"xmlns:xlink"
	]);
	const MUSTACHE_EXPR = seal(/{{[\w\W]*|^[\w\W]*}}/g);
	const ERB_EXPR = seal(/<%[\w\W]*|^[\w\W]*%>/g);
	const TMPLIT_EXPR = seal(/\${[\w\W]*/g);
	const DATA_ATTR = seal(/^data-[\-\w.\u00B7-\uFFFF]+$/);
	const ARIA_ATTR = seal(/^aria-[\-\w]+$/);
	const IS_ALLOWED_URI = seal(/^(?:(?:(?:f|ht)tps?|mailto|tel|callto|sms|cid|xmpp|matrix):|[^a-z]|[a-z+.\-]+(?:[^a-z+.\-:]|$))/i);
	const IS_SCRIPT_OR_DATA = seal(/^(?:\w+script|data):/i);
	const ATTR_WHITESPACE = seal(/[\u0000-\u0020\u00A0\u1680\u180E\u2000-\u2029\u205F\u3000]/g);
	const DOCTYPE_NAME = seal(/^html$/i);
	const CUSTOM_ELEMENT = seal(/^[a-z][.\w]*(-[.\w]+)+$/i);
	const ELEMENT_MARKUP_PROBE = seal(/<[/\w!]/g);
	const COMMENT_MARKUP_PROBE = seal(/<[/\w]/g);
	const FALLBACK_TAG_CLOSE = seal(/<\/no(script|embed|frames)/i);
	const SELF_CLOSING_TAG = seal(/\/>/i);
	const NODE_TYPE = {
		element: 1,
		attribute: 2,
		text: 3,
		cdataSection: 4,
		entityReference: 5,
		entityNode: 6,
		processingInstruction: 7,
		comment: 8,
		document: 9,
		documentType: 10,
		documentFragment: 11,
		notation: 12
	};
	const LITERAL_TEXT_ELEMENT_NAMES = [
		"style",
		"script",
		"xmp",
		"iframe",
		"noembed",
		"noframes",
		"plaintext",
		"noscript"
	];
	const LITERAL_TEXT_ELEMENTS = freeze(addToSet({}, LITERAL_TEXT_ELEMENT_NAMES));
	const LITERAL_TEXT_CLOSE = function() {
		const map = {};
		arrayForEach(LITERAL_TEXT_ELEMENT_NAMES, (name) => {
			map[name] = seal(new RegExp("</" + name + "(?=[\\t\\n\\f\\r />])", "i"));
		});
		return freeze(map);
	}();
	const getGlobal = function getGlobal() {
		return typeof window === "undefined" ? null : window;
	};
	/**
	* Creates a no-op policy for internal use only.
	* Don't export this function outside this module!
	* @param trustedTypes The policy factory.
	* @param purifyHostElement The Script element used to load DOMPurify (to determine policy name suffix).
	* @return The policy created (or null, if Trusted Types
	* are not supported or creating the policy failed).
	*/
	const _createTrustedTypesPolicy = function _createTrustedTypesPolicy(trustedTypes, purifyHostElement) {
		if (typeof trustedTypes !== "object" || typeof trustedTypes.createPolicy !== "function") return null;
		let suffix = null;
		const ATTR_NAME = "data-tt-policy-suffix";
		if (purifyHostElement && purifyHostElement.hasAttribute(ATTR_NAME)) suffix = purifyHostElement.getAttribute(ATTR_NAME);
		const policyName = "dompurify" + (suffix ? "#" + suffix : "");
		try {
			return trustedTypes.createPolicy(policyName, {
				createHTML(html) {
					return html;
				},
				createScriptURL(scriptUrl) {
					return scriptUrl;
				}
			});
		} catch (_) {
			console.warn("TrustedTypes policy " + policyName + " could not be created.");
			return null;
		}
	};
	const _createHooksMap = function _createHooksMap() {
		return {
			afterSanitizeAttributes: [],
			afterSanitizeElements: [],
			afterSanitizeShadowDOM: [],
			beforeSanitizeAttributes: [],
			beforeSanitizeElements: [],
			beforeSanitizeShadowDOM: [],
			uponSanitizeAttribute: [],
			uponSanitizeElement: [],
			uponSanitizeShadowNode: []
		};
	};
	/**
	* Resolve a set-valued configuration option: a fresh set built from
	* cfg[key] when it is an own array property (seeded with a clone of
	* options.base when given, case-normalized via options.transform),
	* the fallback set otherwise.
	*
	* @param cfg the cloned, prototype-free configuration object
	* @param key the configuration property to read
	* @param fallback the set to use when the option is absent or not an array
	* @param options transform and optional base set to merge into
	* @returns the resolved set
	*/
	const _resolveSetOption = function _resolveSetOption(cfg, key, fallback, options) {
		return objectHasOwnProperty(cfg, key) && arrayIsArray(cfg[key]) ? addToSet(options.base ? clone(options.base) : {}, cfg[key], options.transform) : fallback;
	};
	/**
	* Resolve an object-valued configuration option: a prototype-free clone
	* of cfg[key] when it is an own, truthy object property, else a fresh
	* fallback built by makeFallback (fresh on every parse, so a previous
	* parse can never leak state into the next one).
	*
	* @param cfg the cloned, prototype-free configuration object
	* @param key the configuration property to read
	* @param makeFallback builds the fallback value when the option is absent
	* @returns the resolved object
	*/
	const _resolveObjectOption = function _resolveObjectOption(cfg, key, makeFallback) {
		const value = objectHasOwnProperty(cfg, key) ? cfg[key] : void 0;
		return value && typeof value === "object" ? clone(value) : makeFallback();
	};
	function createDOMPurify() {
		let window = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : getGlobal();
		const DOMPurify = (root) => createDOMPurify(root);
		DOMPurify.version = "3.4.16";
		DOMPurify.removed = [];
		if (!window || !window.document || window.document.nodeType !== NODE_TYPE.document || !window.Element) {
			DOMPurify.isSupported = false;
			return DOMPurify;
		}
		let document = window.document;
		const originalDocument = document;
		const currentScript = originalDocument.currentScript;
		window.DocumentFragment;
		const HTMLTemplateElement = window.HTMLTemplateElement, Node = window.Node, Element = window.Element, NodeFilter = window.NodeFilter;
		window.NamedNodeMap === void 0 && (window.NamedNodeMap || window.MozNamedAttrMap);
		window.HTMLFormElement;
		const DOMParser = window.DOMParser, trustedTypes = window.trustedTypes;
		const ElementPrototype = Element.prototype;
		const cloneNode = lookupGetter(ElementPrototype, "cloneNode");
		const remove = lookupGetter(ElementPrototype, "remove");
		const removeAttributeNode = lookupGetter(ElementPrototype, "removeAttributeNode");
		const getNextSibling = lookupGetter(ElementPrototype, "nextSibling");
		const getChildNodes = lookupGetter(ElementPrototype, "childNodes");
		const getParentNode = lookupGetter(ElementPrototype, "parentNode");
		const getShadowRoot = lookupGetter(ElementPrototype, "shadowRoot");
		const getAttributes = lookupGetter(ElementPrototype, "attributes");
		const getNodeType = Node && Node.prototype ? lookupGetter(Node.prototype, "nodeType") : null;
		const getNodeName = Node && Node.prototype ? lookupGetter(Node.prototype, "nodeName") : null;
		const getOwnerDocument = Node && Node.prototype ? lookupGetter(Node.prototype, "ownerDocument") : null;
		const _readNodeType = function _readNodeType(node) {
			return getNodeType ? getNodeType(node) : node.nodeType;
		};
		const _readNodeName = function _readNodeName(node) {
			return getNodeName ? getNodeName(node) : node.nodeName;
		};
		if (typeof HTMLTemplateElement === "function") {
			const template = document.createElement("template");
			if (template.content && template.content.ownerDocument) document = template.content.ownerDocument;
		}
		let trustedTypesPolicy;
		let emptyHTML = "";
		let defaultTrustedTypesPolicy;
		let defaultTrustedTypesPolicyResolved = false;
		let IN_TRUSTED_TYPES_POLICY = 0;
		const _assertNotInTrustedTypesPolicy = function _assertNotInTrustedTypesPolicy() {
			if (IN_TRUSTED_TYPES_POLICY > 0) throw typeErrorCreate("A configured TRUSTED_TYPES_POLICY callback (createHTML or createScriptURL) must not call DOMPurify.sanitize, as that causes infinite recursion. Do not pass a policy whose callbacks wrap DOMPurify as TRUSTED_TYPES_POLICY; see the \"DOMPurify and Trusted Types\" section of the README.");
		};
		const _createTrustedHTML = function _createTrustedHTML(html) {
			_assertNotInTrustedTypesPolicy();
			IN_TRUSTED_TYPES_POLICY++;
			try {
				return trustedTypesPolicy.createHTML(html);
			} finally {
				IN_TRUSTED_TYPES_POLICY--;
			}
		};
		const _createTrustedScriptURL = function _createTrustedScriptURL(scriptUrl) {
			_assertNotInTrustedTypesPolicy();
			IN_TRUSTED_TYPES_POLICY++;
			try {
				return trustedTypesPolicy.createScriptURL(scriptUrl);
			} finally {
				IN_TRUSTED_TYPES_POLICY--;
			}
		};
		const _getDefaultTrustedTypesPolicy = function _getDefaultTrustedTypesPolicy() {
			if (!defaultTrustedTypesPolicyResolved) {
				defaultTrustedTypesPolicy = _createTrustedTypesPolicy(trustedTypes, currentScript);
				defaultTrustedTypesPolicyResolved = true;
			}
			return defaultTrustedTypesPolicy;
		};
		const _document = document, implementation = _document.implementation, createNodeIterator = _document.createNodeIterator, createDocumentFragment = _document.createDocumentFragment, getElementsByTagName = _document.getElementsByTagName;
		const importNode = originalDocument.importNode;
		let hooks = _createHooksMap();
		/**
		* Expose whether this browser supports running the full DOMPurify.
		*/
		DOMPurify.isSupported = typeof entries === "function" && typeof getParentNode === "function" && implementation && implementation.createHTMLDocument !== void 0;
		const MUSTACHE_EXPR$1 = MUSTACHE_EXPR, ERB_EXPR$1 = ERB_EXPR, TMPLIT_EXPR$1 = TMPLIT_EXPR, DATA_ATTR$1 = DATA_ATTR, ARIA_ATTR$1 = ARIA_ATTR, IS_SCRIPT_OR_DATA$1 = IS_SCRIPT_OR_DATA, ATTR_WHITESPACE$1 = ATTR_WHITESPACE, CUSTOM_ELEMENT$1 = CUSTOM_ELEMENT;
		let IS_ALLOWED_URI$1 = IS_ALLOWED_URI;
		/**
		* We consider the elements and attributes below to be safe. Ideally
		* don't add any new ones but feel free to remove unwanted ones.
		*/
		let ALLOWED_TAGS = null;
		const DEFAULT_ALLOWED_TAGS = addToSet({}, [
			...html$1,
			...svg$1,
			...svgFilters,
			...mathMl$1,
			...text
		]);
		let ALLOWED_ATTR = null;
		const DEFAULT_ALLOWED_ATTR = addToSet({}, [
			...html,
			...svg,
			...mathMl,
			...xml
		]);
		let CUSTOM_ELEMENT_HANDLING = Object.seal(create(null, {
			tagNameCheck: {
				writable: true,
				configurable: false,
				enumerable: true,
				value: null
			},
			attributeNameCheck: {
				writable: true,
				configurable: false,
				enumerable: true,
				value: null
			},
			allowCustomizedBuiltInElements: {
				writable: true,
				configurable: false,
				enumerable: true,
				value: false
			}
		}));
		let FORBID_TAGS = null;
		let FORBID_ATTR = null;
		const EXTRA_ELEMENT_HANDLING = Object.seal(create(null, {
			tagCheck: {
				writable: true,
				configurable: false,
				enumerable: true,
				value: null
			},
			attributeCheck: {
				writable: true,
				configurable: false,
				enumerable: true,
				value: null
			}
		}));
		let ALLOW_ARIA_ATTR = true;
		let ALLOW_DATA_ATTR = true;
		let ALLOW_UNKNOWN_PROTOCOLS = false;
		let ALLOW_SELF_CLOSE_IN_ATTR = true;
		let SAFE_FOR_TEMPLATES = false;
		let SAFE_FOR_XML = true;
		let WHOLE_DOCUMENT = false;
		let SET_CONFIG = false;
		let SET_CONFIG_ALLOWED_TAGS = null;
		let SET_CONFIG_ALLOWED_ATTR = null;
		let FORCE_BODY = false;
		let RETURN_DOM = false;
		let RETURN_DOM_FRAGMENT = false;
		let RETURN_TRUSTED_TYPE = false;
		let SANITIZE_DOM = true;
		let SANITIZE_NAMED_PROPS = false;
		const SANITIZE_NAMED_PROPS_PREFIX = "user-content-";
		let KEEP_CONTENT = true;
		let IN_PLACE = false;
		let USE_PROFILES = {};
		let FORBID_CONTENTS = null;
		const DEFAULT_FORBID_CONTENTS = addToSet({}, [
			"annotation-xml",
			"audio",
			"colgroup",
			"desc",
			"foreignobject",
			"head",
			"iframe",
			"math",
			"mi",
			"mn",
			"mo",
			"ms",
			"mtext",
			"noembed",
			"noframes",
			"noscript",
			"plaintext",
			"script",
			"selectedcontent",
			"style",
			"svg",
			"template",
			"thead",
			"title",
			"video",
			"xmp"
		]);
		let DATA_URI_TAGS = null;
		const DEFAULT_DATA_URI_TAGS = addToSet({}, [
			"audio",
			"video",
			"img",
			"source",
			"image",
			"track"
		]);
		let URI_SAFE_ATTRIBUTES = null;
		const DEFAULT_URI_SAFE_ATTRIBUTES = addToSet({}, [
			"alt",
			"class",
			"for",
			"id",
			"label",
			"name",
			"pattern",
			"placeholder",
			"role",
			"summary",
			"title",
			"value",
			"style",
			"xmlns"
		]);
		const MATHML_NAMESPACE = "http://www.w3.org/1998/Math/MathML";
		const SVG_NAMESPACE = "http://www.w3.org/2000/svg";
		const HTML_NAMESPACE = "http://www.w3.org/1999/xhtml";
		let NAMESPACE = HTML_NAMESPACE;
		let IS_EMPTY_INPUT = false;
		let ALLOWED_NAMESPACES = null;
		const DEFAULT_ALLOWED_NAMESPACES = addToSet({}, [
			MATHML_NAMESPACE,
			SVG_NAMESPACE,
			HTML_NAMESPACE
		], stringToString);
		const DEFAULT_MATHML_TEXT_INTEGRATION_POINTS = freeze([
			"mi",
			"mo",
			"mn",
			"ms",
			"mtext"
		]);
		let MATHML_TEXT_INTEGRATION_POINTS = addToSet({}, DEFAULT_MATHML_TEXT_INTEGRATION_POINTS);
		const DEFAULT_HTML_INTEGRATION_POINTS = freeze(["annotation-xml"]);
		let HTML_INTEGRATION_POINTS = addToSet({}, DEFAULT_HTML_INTEGRATION_POINTS);
		const COMMON_SVG_AND_HTML_ELEMENTS = addToSet({}, [
			"title",
			"style",
			"font",
			"a",
			"script"
		]);
		let PARSER_MEDIA_TYPE = null;
		const SUPPORTED_PARSER_MEDIA_TYPES = ["application/xhtml+xml", "text/html"];
		const DEFAULT_PARSER_MEDIA_TYPE = "text/html";
		let transformCaseFunc = null;
		let CONFIG = null;
		const formElement = document.createElement("form");
		const isRegexOrFunction = function isRegexOrFunction(testValue) {
			return testValue instanceof RegExp || testValue instanceof Function;
		};
		/**
		* _parseConfig
		*
		* @param cfg optional config literal
		*/
		const _parseConfig = function _parseConfig() {
			let cfg = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {};
			if (CONFIG && CONFIG === cfg) return;
			if (!cfg || typeof cfg !== "object") cfg = {};
			cfg = clone(cfg);
			PARSER_MEDIA_TYPE = SUPPORTED_PARSER_MEDIA_TYPES.indexOf(cfg.PARSER_MEDIA_TYPE) === -1 ? DEFAULT_PARSER_MEDIA_TYPE : cfg.PARSER_MEDIA_TYPE;
			transformCaseFunc = PARSER_MEDIA_TYPE === "application/xhtml+xml" ? stringToString : stringToLowerCase;
			ALLOWED_TAGS = _resolveSetOption(cfg, "ALLOWED_TAGS", DEFAULT_ALLOWED_TAGS, { transform: transformCaseFunc });
			ALLOWED_ATTR = _resolveSetOption(cfg, "ALLOWED_ATTR", DEFAULT_ALLOWED_ATTR, { transform: transformCaseFunc });
			ALLOWED_NAMESPACES = _resolveSetOption(cfg, "ALLOWED_NAMESPACES", DEFAULT_ALLOWED_NAMESPACES, { transform: stringToString });
			URI_SAFE_ATTRIBUTES = _resolveSetOption(cfg, "ADD_URI_SAFE_ATTR", DEFAULT_URI_SAFE_ATTRIBUTES, {
				transform: transformCaseFunc,
				base: DEFAULT_URI_SAFE_ATTRIBUTES
			});
			DATA_URI_TAGS = _resolveSetOption(cfg, "ADD_DATA_URI_TAGS", DEFAULT_DATA_URI_TAGS, {
				transform: transformCaseFunc,
				base: DEFAULT_DATA_URI_TAGS
			});
			FORBID_CONTENTS = _resolveSetOption(cfg, "FORBID_CONTENTS", DEFAULT_FORBID_CONTENTS, { transform: transformCaseFunc });
			FORBID_TAGS = _resolveSetOption(cfg, "FORBID_TAGS", clone({}), { transform: transformCaseFunc });
			FORBID_ATTR = _resolveSetOption(cfg, "FORBID_ATTR", clone({}), { transform: transformCaseFunc });
			USE_PROFILES = objectHasOwnProperty(cfg, "USE_PROFILES") ? cfg.USE_PROFILES && typeof cfg.USE_PROFILES === "object" ? clone(cfg.USE_PROFILES) : cfg.USE_PROFILES : false;
			ALLOW_ARIA_ATTR = cfg.ALLOW_ARIA_ATTR !== false;
			ALLOW_DATA_ATTR = cfg.ALLOW_DATA_ATTR !== false;
			ALLOW_UNKNOWN_PROTOCOLS = cfg.ALLOW_UNKNOWN_PROTOCOLS || false;
			ALLOW_SELF_CLOSE_IN_ATTR = cfg.ALLOW_SELF_CLOSE_IN_ATTR !== false;
			SAFE_FOR_TEMPLATES = cfg.SAFE_FOR_TEMPLATES || false;
			SAFE_FOR_XML = cfg.SAFE_FOR_XML !== false;
			WHOLE_DOCUMENT = cfg.WHOLE_DOCUMENT || false;
			RETURN_DOM = cfg.RETURN_DOM || false;
			RETURN_DOM_FRAGMENT = cfg.RETURN_DOM_FRAGMENT || false;
			RETURN_TRUSTED_TYPE = cfg.RETURN_TRUSTED_TYPE || false;
			FORCE_BODY = cfg.FORCE_BODY || false;
			SANITIZE_DOM = cfg.SANITIZE_DOM !== false;
			SANITIZE_NAMED_PROPS = cfg.SANITIZE_NAMED_PROPS || false;
			KEEP_CONTENT = cfg.KEEP_CONTENT !== false;
			IN_PLACE = cfg.IN_PLACE || false;
			IS_ALLOWED_URI$1 = isRegex(cfg.ALLOWED_URI_REGEXP) ? cfg.ALLOWED_URI_REGEXP : IS_ALLOWED_URI;
			NAMESPACE = typeof cfg.NAMESPACE === "string" ? cfg.NAMESPACE : HTML_NAMESPACE;
			MATHML_TEXT_INTEGRATION_POINTS = _resolveObjectOption(cfg, "MATHML_TEXT_INTEGRATION_POINTS", () => addToSet({}, DEFAULT_MATHML_TEXT_INTEGRATION_POINTS));
			HTML_INTEGRATION_POINTS = _resolveObjectOption(cfg, "HTML_INTEGRATION_POINTS", () => addToSet({}, DEFAULT_HTML_INTEGRATION_POINTS));
			const customElementHandling = _resolveObjectOption(cfg, "CUSTOM_ELEMENT_HANDLING", () => create(null));
			CUSTOM_ELEMENT_HANDLING = create(null);
			if (objectHasOwnProperty(customElementHandling, "tagNameCheck") && isRegexOrFunction(customElementHandling.tagNameCheck)) CUSTOM_ELEMENT_HANDLING.tagNameCheck = customElementHandling.tagNameCheck;
			if (objectHasOwnProperty(customElementHandling, "attributeNameCheck") && isRegexOrFunction(customElementHandling.attributeNameCheck)) CUSTOM_ELEMENT_HANDLING.attributeNameCheck = customElementHandling.attributeNameCheck;
			if (objectHasOwnProperty(customElementHandling, "allowCustomizedBuiltInElements") && typeof customElementHandling.allowCustomizedBuiltInElements === "boolean") CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements = customElementHandling.allowCustomizedBuiltInElements;
			seal(CUSTOM_ELEMENT_HANDLING);
			if (SAFE_FOR_TEMPLATES) ALLOW_DATA_ATTR = false;
			if (RETURN_DOM_FRAGMENT) RETURN_DOM = true;
			if (USE_PROFILES) {
				ALLOWED_TAGS = addToSet({}, text);
				ALLOWED_ATTR = create(null);
				if (USE_PROFILES.html === true) {
					addToSet(ALLOWED_TAGS, html$1);
					addToSet(ALLOWED_ATTR, html);
				}
				if (USE_PROFILES.svg === true) {
					addToSet(ALLOWED_TAGS, svg$1);
					addToSet(ALLOWED_ATTR, svg);
					addToSet(ALLOWED_ATTR, xml);
				}
				if (USE_PROFILES.svgFilters === true) {
					addToSet(ALLOWED_TAGS, svgFilters);
					addToSet(ALLOWED_ATTR, svg);
					addToSet(ALLOWED_ATTR, xml);
				}
				if (USE_PROFILES.mathMl === true) {
					addToSet(ALLOWED_TAGS, mathMl$1);
					addToSet(ALLOWED_ATTR, mathMl);
					addToSet(ALLOWED_ATTR, xml);
				}
			}
			EXTRA_ELEMENT_HANDLING.tagCheck = null;
			EXTRA_ELEMENT_HANDLING.attributeCheck = null;
			if (objectHasOwnProperty(cfg, "ADD_TAGS")) {
				if (typeof cfg.ADD_TAGS === "function") EXTRA_ELEMENT_HANDLING.tagCheck = cfg.ADD_TAGS;
				else if (arrayIsArray(cfg.ADD_TAGS)) {
					if (ALLOWED_TAGS === DEFAULT_ALLOWED_TAGS) ALLOWED_TAGS = clone(ALLOWED_TAGS);
					addToSet(ALLOWED_TAGS, cfg.ADD_TAGS, transformCaseFunc);
				}
			}
			if (objectHasOwnProperty(cfg, "ADD_ATTR")) {
				if (typeof cfg.ADD_ATTR === "function") EXTRA_ELEMENT_HANDLING.attributeCheck = cfg.ADD_ATTR;
				else if (arrayIsArray(cfg.ADD_ATTR)) {
					if (ALLOWED_ATTR === DEFAULT_ALLOWED_ATTR) ALLOWED_ATTR = clone(ALLOWED_ATTR);
					addToSet(ALLOWED_ATTR, cfg.ADD_ATTR, transformCaseFunc);
				}
			}
			if (objectHasOwnProperty(cfg, "ADD_FORBID_CONTENTS") && arrayIsArray(cfg.ADD_FORBID_CONTENTS)) {
				if (FORBID_CONTENTS === DEFAULT_FORBID_CONTENTS) FORBID_CONTENTS = clone(FORBID_CONTENTS);
				addToSet(FORBID_CONTENTS, cfg.ADD_FORBID_CONTENTS, transformCaseFunc);
			}
			if (KEEP_CONTENT) ALLOWED_TAGS["#text"] = true;
			if (WHOLE_DOCUMENT) addToSet(ALLOWED_TAGS, [
				"html",
				"head",
				"body"
			]);
			if (ALLOWED_TAGS.table) {
				addToSet(ALLOWED_TAGS, ["tbody"]);
				delete FORBID_TAGS.tbody;
			}
			if (cfg.TRUSTED_TYPES_POLICY) {
				if (typeof cfg.TRUSTED_TYPES_POLICY.createHTML !== "function") throw typeErrorCreate("TRUSTED_TYPES_POLICY configuration option must provide a \"createHTML\" hook.");
				if (typeof cfg.TRUSTED_TYPES_POLICY.createScriptURL !== "function") throw typeErrorCreate("TRUSTED_TYPES_POLICY configuration option must provide a \"createScriptURL\" hook.");
				const previousTrustedTypesPolicy = trustedTypesPolicy;
				trustedTypesPolicy = cfg.TRUSTED_TYPES_POLICY;
				try {
					emptyHTML = _createTrustedHTML("");
				} catch (error) {
					trustedTypesPolicy = previousTrustedTypesPolicy;
					throw error;
				}
			} else if (cfg.TRUSTED_TYPES_POLICY === null) {
				trustedTypesPolicy = void 0;
				emptyHTML = "";
			} else {
				if (trustedTypesPolicy === void 0) trustedTypesPolicy = _getDefaultTrustedTypesPolicy();
				if (trustedTypesPolicy && typeof emptyHTML === "string") emptyHTML = _createTrustedHTML("");
			}
			if (freeze) freeze(cfg);
			CONFIG = cfg;
		};
		const ALL_SVG_TAGS = addToSet({}, [
			...svg$1,
			...svgFilters,
			...svgDisallowed
		]);
		const ALL_MATHML_TAGS = addToSet({}, [...mathMl$1, ...mathMlDisallowed]);
		/**
		* Namespace rules for an element in the SVG namespace.
		*
		* @param tagName the element's lowercase tag name
		* @param parent the (possibly simulated) parent node
		* @param parentTagName the parent's lowercase tag name
		* @returns true if a spec-compliant parser could produce this element
		*/
		const _checkSvgNamespace = function _checkSvgNamespace(tagName, parent, parentTagName) {
			if (parent.namespaceURI === HTML_NAMESPACE) return tagName === "svg";
			if (parent.namespaceURI === MATHML_NAMESPACE) return tagName === "svg" && (parentTagName === "annotation-xml" || MATHML_TEXT_INTEGRATION_POINTS[parentTagName]);
			return Boolean(ALL_SVG_TAGS[tagName]);
		};
		/**
		* Namespace rules for an element in the MathML namespace.
		*
		* @param tagName the element's lowercase tag name
		* @param parent the (possibly simulated) parent node
		* @param parentTagName the parent's lowercase tag name
		* @returns true if a spec-compliant parser could produce this element
		*/
		const _checkMathMlNamespace = function _checkMathMlNamespace(tagName, parent, parentTagName) {
			if (parent.namespaceURI === HTML_NAMESPACE) return tagName === "math";
			if (parent.namespaceURI === SVG_NAMESPACE) return tagName === "math" && HTML_INTEGRATION_POINTS[parentTagName];
			return Boolean(ALL_MATHML_TAGS[tagName]);
		};
		/**
		* Namespace rules for an element in the HTML namespace.
		*
		* @param tagName the element's lowercase tag name
		* @param parent the (possibly simulated) parent node
		* @param parentTagName the parent's lowercase tag name
		* @returns true if a spec-compliant parser could produce this element
		*/
		const _checkHtmlNamespace = function _checkHtmlNamespace(tagName, parent, parentTagName) {
			if (parent.namespaceURI === SVG_NAMESPACE && !HTML_INTEGRATION_POINTS[parentTagName]) return false;
			if (parent.namespaceURI === MATHML_NAMESPACE && !MATHML_TEXT_INTEGRATION_POINTS[parentTagName]) return false;
			return !ALL_MATHML_TAGS[tagName] && (COMMON_SVG_AND_HTML_ELEMENTS[tagName] || !ALL_SVG_TAGS[tagName]);
		};
		/**
		* @param element a DOM element whose namespace is being checked
		* @returns Return false if the element has a
		*  namespace that a spec-compliant parser would never
		*  return. Return true otherwise.
		*/
		const _checkValidNamespace = function _checkValidNamespace(element) {
			let parent = getParentNode(element);
			if (!parent || !parent.tagName) parent = {
				namespaceURI: NAMESPACE,
				tagName: "template"
			};
			const tagName = stringToLowerCase(element.tagName);
			const parentTagName = stringToLowerCase(parent.tagName);
			if (!ALLOWED_NAMESPACES[element.namespaceURI]) return false;
			if (element.namespaceURI === SVG_NAMESPACE) return _checkSvgNamespace(tagName, parent, parentTagName);
			if (element.namespaceURI === MATHML_NAMESPACE) return _checkMathMlNamespace(tagName, parent, parentTagName);
			if (element.namespaceURI === HTML_NAMESPACE) return _checkHtmlNamespace(tagName, parent, parentTagName);
			if (PARSER_MEDIA_TYPE === "application/xhtml+xml" && ALLOWED_NAMESPACES[element.namespaceURI]) return true;
			return false;
		};
		/**
		* _forceRemove
		*
		* @param node a DOM node
		*/
		const _forceRemove = function _forceRemove(node) {
			arrayPush(DOMPurify.removed, { element: node });
			try {
				getParentNode(node).removeChild(node);
			} catch (_) {
				remove(node);
				if (!getParentNode(node)) throw typeErrorCreate("a node selected for removal could not be detached from its tree and cannot be safely returned; refusing to sanitize in place");
			}
		};
		/**
		* _stripAttributeNode
		*
		* Remove a single Attr node case/namespace-exactly on an attribute-teardown
		* path. Name-based removeAttribute() ASCII-lowercases its lookup key for an
		* HTML element in an HTML document and so silently misses a case-preserved
		* handler (e.g. `ONERROR` off an XML/XHTML import) - the same defect
		* _removeAttribute() was fixed for, which a name-based call would reintroduce
		* on these IN_PLACE teardown paths. Unlike _removeAttribute this does not
		* record into DOMPurify.removed: the neutralize passes intentionally do not
		* book-keep. A clobbered/detached node falls back to best-effort name-based
		* removal.
		*
		* @param element the element to strip the attribute from
		* @param attribute the Attr node to remove
		* @param name the attribute's name, for the fallback path
		*/
		const _stripAttributeNode = function _stripAttributeNode(element, attribute, name) {
			try {
				removeAttributeNode(element, attribute);
			} catch (_) {
				try {
					element.removeAttribute(name);
				} catch (_) {}
			}
		};
		/**
		* _neutralizeRoot
		*
		* Fail-closed teardown of an in-place root after the sanitize walk aborts
		* (campaign-3 F2). An internal throw mid-walk — e.g. a page-registered
		* custom element's reaction detaches a node so `_forceRemove`'s deliberate
		* parentless guard throws, or any other re-entrant engine mutation — would
		* otherwise leave the caller's *live* tree half-sanitized, with everything
		* after the abort point still carrying its handlers. There is no safe way
		* to resume the walk (the tree mutated under us), so we strip the root bare:
		* remove every child and every attribute, then let the caller's catch see
		* the original error. Clobber-safe (cached `remove`/`childNodes`/`attributes`
		* getters; the root was already clobber-pre-flighted at the IN_PLACE entry).
		*
		* @param root the in-place root to empty
		*/
		const _neutralizeRoot = function _neutralizeRoot(root) {
			_neutralizeSubtree(root);
			const childNodes = getChildNodes(root);
			if (childNodes) {
				const snapshot = [];
				arrayForEach(childNodes, (child) => {
					arrayPush(snapshot, child);
				});
				arrayForEach(snapshot, (child) => {
					try {
						remove(child);
					} catch (_) {}
				});
			}
			const attributes = getAttributes(root);
			if (attributes) for (let i = attributes.length - 1; i >= 0; --i) {
				const attribute = attributes[i];
				const name = attribute && attribute.name;
				if (typeof name === "string") _stripAttributeNode(root, attribute, name);
			}
		};
		/**
		* _removeAttribute
		*
		* Name-based getAttributeNode()/removeAttribute() ASCII-lowercase their
		* lookup key for HTML elements in an HTML document, so they silently miss an
		* attribute whose stored qualified name still contains uppercase ASCII
		* letters. That happens when the node came from a case-preserving source
		* (an XML/XHTML document imported via importNode(), or createAttributeNS()),
		* where e.g. `ONERROR` survives the walk: the policy check lowercases to
		* `onerror` and rejects it, but `removeAttribute('ONERROR')` looks up
		* `onerror` and finds nothing. Remove the exact Attr node instead, which is
		* case- and namespace-exact, and fall back to name-based removal only when
		* the caller could not supply the node.
		*
		* @param name an Attribute name
		* @param element a DOM node
		* @param attr the exact Attr node to remove, when the caller has it
		*/
		const _removeAttribute = function _removeAttribute(name, element, attr) {
			if (!attr) try {
				attr = element.getAttributeNode(name);
			} catch (_) {
				attr = null;
			}
			arrayPush(DOMPurify.removed, {
				attribute: attr || null,
				from: element
			});
			try {
				if (attr) removeAttributeNode(element, attr);
				else element.removeAttribute(name);
			} catch (_) {
				try {
					element.removeAttribute(name);
				} catch (_) {}
			}
			if (name === "is") {
				if (RETURN_DOM || RETURN_DOM_FRAGMENT) try {
					_forceRemove(element);
				} catch (_) {}
				else try {
					element.setAttribute(name, "");
				} catch (_) {}
			}
		};
		/**
		* _stripDisallowedAttributes
		*
		* Removes every attribute the active configuration does not allow from a
		* single element, using the same allowlist as the main attribute pass (so
		* `on*` handlers go, but no `/^on/` blocklist is introduced). Used only to
		* neutralise nodes that are being discarded from an in-place tree.
		*
		* @param element the element to strip
		*/
		const _stripDisallowedAttributes = function _stripDisallowedAttributes(element) {
			const attributes = getAttributes(element);
			if (!attributes) return;
			for (let i = attributes.length - 1; i >= 0; --i) {
				const attribute = attributes[i];
				const name = attribute && attribute.name;
				if (typeof name !== "string" || ALLOWED_ATTR[transformCaseFunc(name)]) continue;
				_stripAttributeNode(element, attribute, name);
			}
		};
		/**
		* _neutralizeSubtree
		*
		* Completes the audit-5 F1 fix across every removal path. The KEEP_CONTENT
		* move-hoist neutralises only disallowed-tag removals; clobber, mXSS-canary,
		* namespace, comment, processing-instruction and KEEP_CONTENT:false removals
		* all drop their subtree wholesale via `_forceRemove`. On the IN_PLACE path
		* those dropped nodes are detached from the caller's LIVE tree but a
		* handler-bearing original among them (an `<img onerror>`/`<video>` that was
		* loading) keeps its queued resource event, which fires in page scope after
		* sanitize returns. This walks a removed subtree and strips every attribute
		* the active configuration does not allow — so `on*` handlers are cancelled
		* through the SAME allowlist that governs kept nodes, not a separate `/^on/`
		* blocklist. Run synchronously before sanitize returns, i.e. before any
		* queued event can fire. Hook-free by design: these nodes leave the output,
		* so firing attribute hooks for them would be surprising. Clobber-safe reads;
		* a doomed clobbered node may shadow `removeAttribute` (its own attributes are
		* irrelevant — it is discarded — while its non-clobbered descendants, e.g.
		* the `<img>`, are reached and scrubbed).
		*
		* @param root the root of a removed subtree to neutralise
		*/
		const _neutralizeSubtree = function _neutralizeSubtree(root) {
			const stack = [root];
			while (stack.length > 0) {
				const node = stack.pop();
				if (_readNodeType(node) === NODE_TYPE.element) _stripDisallowedAttributes(node);
				const childNodes = getChildNodes(node);
				if (childNodes) for (let i = childNodes.length - 1; i >= 0; --i) stack.push(childNodes[i]);
			}
		};
		/**
		* _neutralizePatchLinkage
		*
		* IN_PLACE entry pre-pass (declarative-partial-updates / streaming
		* hardening, https://github.com/WICG/declarative-partial-updates).
		*
		* The main walk strips patch linkage (`for`/`patchsrc`) and removes range
		* markers (PIs / markup comments) node-by-node, in document order, AS it
		* reaches each node. On a live in-place root that leaves a window: from the
		* moment the root is connected until the walk arrives at a given node, that
		* node's linkage is live. A patch applied on connection/stream can fire as
		* a microtask during the walk and inject or teleport an unsanitized DOM
		* range into a region the iterator has already passed and will not revisit,
		* so the post-return "tree is sanitized" contract is violated. Sweep the
		* whole tree once up front and sever every linkage before the walk begins,
		* closing that window.
		*
		* This CANNOT undo a patch that already fired before sanitize ran — that is
		* the irreducible "do not IN_PLACE a live-connected attacker tree" caveat —
		* but it closes everything from sanitize-start onward. Gated on SAFE_FOR_XML
		* to group with the rest of the declarative-partial-updates handling and
		* stay overridable, consistent with the codebase.
		*
		* Clobber-safe traversal (cached childNodes getter); per-node try/catch so a
		* clobbered root cannot defeat the sweep of its non-clobbered descendants.
		*
		* NOTE (pending real-Chrome confirmation, see test/declarative-patch-probe
		* .html Q1): this mirrors the existing policy of keeping `for` on
		* <label>/<output>. If the shipping feature can drive a patch through a
		* surviving `for`-on-label/output + `id` pair, this pre-pass and the
		* attribute check at _isBasicCustomElement's caller must additionally drop
		* that pair on the IN_PLACE path. Left as-is until the taxonomy is verified.
		*
		* @param root the in-place root to sweep
		*/
		/**
		* Central policy for declarative-partial-updates patch-linkage attributes,
		* shared by the _neutralizePatchLinkage pre-pass and _isValidAttribute so
		* the two sites cannot drift: `patchsrc` always links, `for` links
		* everywhere except on <label>/<output>, and the whole policy is gated on
		* SAFE_FOR_XML (see the rationale block in _isValidAttribute).
		*
		* @param lcName the transformCaseFunc'd attribute name
		* @param lcTag the transformCaseFunc'd tag name of the carrying element
		* @return true if the attribute is patch linkage and must be dropped
		*/
		const _isPatchLinkageAttribute = function _isPatchLinkageAttribute(lcName, lcTag) {
			if (!SAFE_FOR_XML) return false;
			if (lcName === "patchsrc") return true;
			return lcName === "for" && lcTag !== "label" && lcTag !== "output";
		};
		const _neutralizePatchLinkage = function _neutralizePatchLinkage(root) {
			if (!SAFE_FOR_XML) return;
			const stack = [root];
			while (stack.length > 0) {
				const node = stack.pop();
				const nodeType = _readNodeType(node);
				if (nodeType === NODE_TYPE.processingInstruction || nodeType === NODE_TYPE.comment && regExpTest(COMMENT_MARKUP_PROBE, node.data)) {
					try {
						remove(node);
					} catch (_) {}
					continue;
				}
				if (nodeType === NODE_TYPE.element) {
					const element = node;
					const lcTag = transformCaseFunc(_readNodeName(node));
					try {
						if (element.hasAttribute && element.hasAttribute("patchsrc")) element.removeAttribute("patchsrc");
						if (element.hasAttribute && element.hasAttribute("for") && _isPatchLinkageAttribute("for", lcTag)) element.removeAttribute("for");
					} catch (_) {}
				}
				const childNodes = getChildNodes(node);
				if (childNodes) for (let i = childNodes.length - 1; i >= 0; --i) stack.push(childNodes[i]);
			}
		};
		/**
		* _initDocument
		*
		* @param dirty - a string of dirty markup
		* @return a DOM, filled with the dirty markup
		*/
		const _initDocument = function _initDocument(dirty) {
			let doc = null;
			let leadingWhitespace = null;
			if (FORCE_BODY) dirty = "<remove></remove>" + dirty;
			else {
				const matches = stringMatch(dirty, /^[\r\n\t ]+/);
				leadingWhitespace = matches && matches[0];
			}
			if (PARSER_MEDIA_TYPE === "application/xhtml+xml" && NAMESPACE === HTML_NAMESPACE) dirty = "<html xmlns=\"http://www.w3.org/1999/xhtml\"><head></head><body>" + dirty + "</body></html>";
			const dirtyPayload = trustedTypesPolicy ? _createTrustedHTML(dirty) : dirty;
			if (NAMESPACE === HTML_NAMESPACE) try {
				doc = new DOMParser().parseFromString(dirtyPayload, PARSER_MEDIA_TYPE);
			} catch (_) {}
			if (!doc || !doc.documentElement) {
				doc = implementation.createDocument(NAMESPACE, "template", null);
				try {
					doc.documentElement.innerHTML = IS_EMPTY_INPUT ? emptyHTML : dirtyPayload;
				} catch (_) {}
			}
			const body = doc.body || doc.documentElement;
			if (dirty && leadingWhitespace) body.insertBefore(document.createTextNode(leadingWhitespace), body.childNodes[0] || null);
			if (NAMESPACE === HTML_NAMESPACE) return getElementsByTagName.call(doc, WHOLE_DOCUMENT ? "html" : "body")[0];
			return WHOLE_DOCUMENT ? doc.documentElement : body;
		};
		/**
		* Creates a NodeIterator object that you can use to traverse filtered lists of nodes or elements in a document.
		*
		* @param root The root element or node to start traversing on.
		* @return The created NodeIterator
		*/
		const _createNodeIterator = function _createNodeIterator(root) {
			const doc = getOwnerDocument ? getOwnerDocument(root) : root.ownerDocument;
			return createNodeIterator.call(doc || root, root, NodeFilter.SHOW_ELEMENT | NodeFilter.SHOW_COMMENT | NodeFilter.SHOW_TEXT | NodeFilter.SHOW_PROCESSING_INSTRUCTION | NodeFilter.SHOW_CDATA_SECTION, null);
		};
		/**
		* Replace template expression syntax (mustache, ERB, template
		* literal) with a space; shared by all SAFE_FOR_TEMPLATES scrub
		* sites. Order matters: mustache, then ERB, then template literal.
		*
		* @param value the string to scrub
		* @returns the scrubbed string
		*/
		const _stripTemplateExpressions = function _stripTemplateExpressions(value) {
			value = stringReplace(value, MUSTACHE_EXPR$1, " ");
			value = stringReplace(value, ERB_EXPR$1, " ");
			value = stringReplace(value, TMPLIT_EXPR$1, " ");
			return value;
		};
		/**
		* Strip template-engine expressions ({{...}}, ${...}, <%...%>) from the
		* character data of an element subtree. Used as the final safety net for
		* SAFE_FOR_TEMPLATES on every DOM-returning code path so that expressions
		* which only form after text-node normalization (e.g. fragments split across
		* stripped elements) cannot survive into a template-evaluating framework.
		*
		* Walks text/comment/CDATA/processing-instruction nodes and mutates `.data`
		* in place rather than round-tripping through innerHTML. This preserves
		* descendant node references (important for IN_PLACE callers), avoids a
		* serialize/reparse cycle, and reads literal character data — which means
		* `<%...%>` in text content matches the ERB regex against its real bytes
		* instead of the HTML-entity-escaped form innerHTML would produce.
		*
		* Attribute values are not visited here; SAFE_FOR_TEMPLATES handling for
		* attributes is performed during the per-node `_sanitizeAttributes` pass.
		*
		* @param node The root element whose character data should be scrubbed.
		*/
		const _scrubTemplateExpressions2 = function _scrubTemplateExpressions(node) {
			var _node$querySelectorAl;
			node.normalize();
			const doc = getOwnerDocument ? getOwnerDocument(node) : node.ownerDocument;
			const walker = createNodeIterator.call(doc || node, node, NodeFilter.SHOW_TEXT | NodeFilter.SHOW_COMMENT | NodeFilter.SHOW_CDATA_SECTION | NodeFilter.SHOW_PROCESSING_INSTRUCTION, null);
			let currentNode = walker.nextNode();
			while (currentNode) {
				currentNode.data = _stripTemplateExpressions(currentNode.data);
				currentNode = walker.nextNode();
			}
			const templates = (_node$querySelectorAl = node.querySelectorAll) === null || _node$querySelectorAl === void 0 ? void 0 : _node$querySelectorAl.call(node, "template");
			if (templates) arrayForEach(templates, (tmpl) => {
				if (_isDocumentFragment(tmpl.content)) _scrubTemplateExpressions2(tmpl.content);
			});
		};
		/**
		* _isClobbered
		*
		* Detect DOM-clobbering on HTMLFormElement nodes. Form is the only HTML
		* interface with [LegacyOverrideBuiltIns]; a descendant element with a
		* `name` attribute matching a prototype property shadows that property
		* on direct reads. We use this check at the IN_PLACE entry-point and
		* during attribute sanitization to refuse clobbered forms.
		*
		* @param element element to check for clobbering attacks
		* @return true if clobbered, false if safe
		*/
		const _isClobbered = function _isClobbered(element) {
			const realTagName = getNodeName ? getNodeName(element) : null;
			if (typeof realTagName !== "string") return false;
			if (transformCaseFunc(realTagName) !== "form") return false;
			return typeof element.nodeName !== "string" || typeof element.textContent !== "string" || typeof element.removeChild !== "function" || element.attributes !== getAttributes(element) || typeof element.removeAttribute !== "function" || typeof element.removeAttributeNode !== "function" || typeof element.getAttributeNode !== "function" || typeof element.setAttribute !== "function" || typeof element.namespaceURI !== "string" || typeof element.insertBefore !== "function" || typeof element.hasChildNodes !== "function" || element.nodeType !== getNodeType(element) || element.childNodes !== getChildNodes(element);
		};
		/**
		* Checks whether the given value is a DocumentFragment from any realm.
		*
		* The realm-independent replacement reads `nodeType` through the cached
		* Node.prototype getter and compares to the DOCUMENT_FRAGMENT_NODE
		* constant (11). nodeType is a numeric value resolved from the node's
		* internal slot, identical across realms for the same kind of node.
		*
		* @param value object to check
		* @return true if value is a DocumentFragment-shaped node from any realm
		*/
		const _isDocumentFragment = function _isDocumentFragment(value) {
			if (!getNodeType || typeof value !== "object" || value === null) return false;
			try {
				return getNodeType(value) === NODE_TYPE.documentFragment;
			} catch (_) {
				return false;
			}
		};
		/**
		* Checks whether the given object is a DOM node, including nodes that
		* originate from a different window/realm (e.g. an iframe's
		* contentDocument). The previous `value instanceof Node` check was
		* realm-bound: nodes from a different window failed it, causing
		* sanitize() to silently stringify them and reset IN_PLACE to false,
		* returning the original node unsanitized. See GHSA-4w3q-35jp-p934.
		*
		* @param value object to check whether it's a DOM node
		* @return true if value is a DOM node from any realm
		*/
		const _isNode = function _isNode(value) {
			if (!getNodeType || typeof value !== "object" || value === null) return false;
			try {
				return typeof getNodeType(value) === "number";
			} catch (_) {
				return false;
			}
		};
		function _executeHooks(hooks, currentNode, data) {
			if (hooks.length === 0) return;
			arrayForEach(hooks, (hook) => {
				hook.call(DOMPurify, currentNode, data, CONFIG);
			});
		}
		/**
		* Structural-threat checks that condemn a node regardless of the
		* allowlists: mXSS via namespace confusion, risky CSS construction,
		* processing instructions, markup-bearing comments. Pure predicate;
		* the caller removes. Check order is load-bearing.
		*
		* @param currentNode the node to inspect
		* @param tagName the node's transformCaseFunc'd tag name
		* @return true if the node must be removed
		*/
		const _isUnsafeNode = function _isUnsafeNode(currentNode, tagName) {
			if (SAFE_FOR_XML && currentNode.hasChildNodes() && !_isNode(currentNode.firstElementChild) && regExpTest(ELEMENT_MARKUP_PROBE, currentNode.textContent) && regExpTest(ELEMENT_MARKUP_PROBE, currentNode.innerHTML)) return true;
			if (SAFE_FOR_XML && currentNode.namespaceURI === HTML_NAMESPACE && LITERAL_TEXT_ELEMENTS[tagName] && (_isNode(currentNode.firstElementChild) || typeof currentNode.textContent === "string" && regExpTest(LITERAL_TEXT_CLOSE[tagName], currentNode.textContent))) return true;
			if (currentNode.nodeType === NODE_TYPE.processingInstruction) return true;
			if (SAFE_FOR_XML && currentNode.nodeType === NODE_TYPE.comment && regExpTest(COMMENT_MARKUP_PROBE, currentNode.data)) return true;
			return false;
		};
		/**
		* Evaluate a CUSTOM_ELEMENT_HANDLING check (a RegExp or a predicate
		* function, per the validation in _parseConfig) against a name.
		* Additional arguments are forwarded to predicate functions - the
		* attributeNameCheck predicate receives the tag name as its second
		* argument. A null/absent check never matches.
		*
		* @param check the configured tagNameCheck / attributeNameCheck value
		* @param name the name to test
		* @param args extra arguments forwarded to a predicate function
		* @return true if the check matches the name
		*/
		const _matchesNameCheck = function _matchesNameCheck(check, name) {
			if (check instanceof RegExp) return regExpTest(check, name);
			if (check instanceof Function) {
				for (var _len = arguments.length, args = new Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) args[_key - 2] = arguments[_key];
				return Boolean(check(name, ...args));
			}
			return false;
		};
		/**
		* Handle a node whose tag is forbidden or not allowlisted: keep
		* allowed custom elements (false return exits _sanitizeElements
		* early - the namespace and fallback-tag removal checks are
		* intentionally skipped for kept custom elements), else hoist
		* content per KEEP_CONTENT and remove.
		*
		* A kept custom element is the ONLY case in which this function
		* returns false, so the caller uses that return value to run the
		* afterSanitizeElements hook on the kept element and keep the
		* element-hook lifecycle consistent with normal allowlisted
		* elements (GHSA-c2j3-45gr-mqc4).
		*
		* @param currentNode the disallowed node
		* @param tagName the node's transformCaseFunc'd tag name
		* @return true if the node was removed, false if kept
		*/
		const _sanitizeDisallowedNode = function _sanitizeDisallowedNode(currentNode, tagName, root) {
			if (!FORBID_TAGS[tagName] && _isBasicCustomElement(tagName) && _matchesNameCheck(CUSTOM_ELEMENT_HANDLING.tagNameCheck, tagName)) return false;
			if (KEEP_CONTENT && !FORBID_CONTENTS[tagName]) {
				const parentNode = getParentNode(currentNode);
				const childNodes = getChildNodes(currentNode);
				if (childNodes && parentNode) {
					const childCount = childNodes.length;
					for (let i = childCount - 1; i >= 0; --i) {
						const hoisted = currentNode === root ? cloneNode(childNodes[i], true) : childNodes[i];
						parentNode.insertBefore(hoisted, getNextSibling(currentNode));
					}
				}
			}
			_forceRemove(currentNode);
			return true;
		};
		/**
		* Fork a hook-mutable allowlist off its shared binding the first time a
		* (possibly lazily-installed) uponSanitize* hook is about to see it, so the
		* hook cannot widen the per-instance default or the setConfig binding by
		* reference and leak past the call. Returns the set unchanged once it is
		* already call-local, so repeated calls across elements are idempotent.
		*
		* @param hookList the uponSanitize* hook array for this event
		* @param set the current ALLOWED_TAGS / ALLOWED_ATTR binding
		* @param defaultSet the per-instance DEFAULT_ALLOWED_* constant
		* @param setConfigSet the captured setConfig() binding, or null
		* @return a call-local clone if a hook is present and set is still shared,
		*   else set unchanged
		*/
		const _forkSharedAllowlist = function _forkSharedAllowlist(hookList, set, defaultSet, setConfigSet) {
			if (hookList.length === 0) return set;
			return set === defaultSet || set === setConfigSet ? clone(set) : set;
		};
		/**
		* Shared guard for a node that a hook has detached from the walk tree,
		* used after each element-hook site in _sanitizeElements. Detaching is a
		* long-standing user pattern (issue #469; draw.io-style foreignObject
		* filtering). Per the cached, unclobberable parentNode getter the node is
		* genuinely out of the tree, so it can reach neither the serialized
		* output nor an IN_PLACE live tree; treat it as removed and stop
		* processing it. Without this guard, the unsafe-node / namespace checks
		* would call _forceRemove on a parentless node and hit the REPORT-3
		* fail-closed throw — which exists for nodes DOMPurify wants gone but
		* *cannot* detach (clobbered / parentless roots), the opposite of a node
		* that is already safely gone. The walk root is exempt: a detached
		* IN_PLACE root is legitimate input and must still be fully sanitized,
		* and a kill-decision on it must keep hitting the REPORT-3 throw.
		*
		* Nodes detached by hooks stay the hook's responsibility for placement:
		* they are not recorded in DOMPurify.removed, so the post-walk IN_PLACE
		* pass (which iterates DOMPurify.removed) does not reach them. But a
		* hook-detached subtree can still hold a queued resource-event handler -
		* e.g. an <img onload> that began loading when the caller built the live
		* tree - which fires in page scope after sanitize returns even though the
		* handler never reached the returned tree. That is the audit-5 F1 hazard,
		* and the documented node.remove() hook pattern walks straight into it.
		* So on the IN_PLACE path we neutralize the detached subtree inline,
		* stripping its non-allow-listed attributes before returning, exactly as
		* the post-walk pass does for _forceRemove'd subtrees.
		*
		* @param currentNode the node a hook may have detached
		* @param root the current walk root
		* @return true if the node is detached and now handled, false otherwise
		*/
		const _handleHookDetachedNode = function _handleHookDetachedNode(currentNode, root) {
			if (currentNode === root || getParentNode(currentNode) !== null) return false;
			if (IN_PLACE) _neutralizeSubtree(currentNode);
			return true;
		};
		/**
		* _sanitizeElements
		*
		* @protect nodeName
		* @protect textContent
		* @protect removeChild
		* @param currentNode to check for permission to exist
		* @return true if node was killed, false if left alive
		*/
		const _sanitizeElements = function _sanitizeElements(currentNode, root) {
			_executeHooks(hooks.beforeSanitizeElements, currentNode, null);
			if (_handleHookDetachedNode(currentNode, root)) return true;
			if (_isClobbered(currentNode)) {
				_forceRemove(currentNode);
				return true;
			}
			const tagName = transformCaseFunc(_readNodeName(currentNode));
			ALLOWED_TAGS = _forkSharedAllowlist(hooks.uponSanitizeElement, ALLOWED_TAGS, DEFAULT_ALLOWED_TAGS, SET_CONFIG_ALLOWED_TAGS);
			_executeHooks(hooks.uponSanitizeElement, currentNode, {
				tagName,
				allowedTags: ALLOWED_TAGS
			});
			if (_handleHookDetachedNode(currentNode, root)) return true;
			if (_isUnsafeNode(currentNode, tagName)) {
				_forceRemove(currentNode);
				return true;
			}
			if (FORBID_TAGS[tagName] || !(EXTRA_ELEMENT_HANDLING.tagCheck instanceof Function && EXTRA_ELEMENT_HANDLING.tagCheck(tagName)) && !ALLOWED_TAGS[tagName]) {
				const removed = _sanitizeDisallowedNode(currentNode, tagName, root);
				if (removed === false) {
					_executeHooks(hooks.afterSanitizeElements, currentNode, null);
					if (_handleHookDetachedNode(currentNode, root)) return true;
				}
				return removed;
			}
			if (_readNodeType(currentNode) === NODE_TYPE.element && !_checkValidNamespace(currentNode)) {
				_forceRemove(currentNode);
				return true;
			}
			if ((tagName === "noscript" || tagName === "noembed" || tagName === "noframes") && regExpTest(FALLBACK_TAG_CLOSE, currentNode.innerHTML)) {
				_forceRemove(currentNode);
				return true;
			}
			if (SAFE_FOR_TEMPLATES && currentNode.nodeType === NODE_TYPE.text) {
				const content = _stripTemplateExpressions(currentNode.textContent);
				if (currentNode.textContent !== content) {
					arrayPush(DOMPurify.removed, { element: currentNode.cloneNode() });
					currentNode.textContent = content;
				}
			}
			_executeHooks(hooks.afterSanitizeElements, currentNode, null);
			return _handleHookDetachedNode(currentNode, root);
		};
		/**
		* _isValidAttribute
		*
		* @param lcTag Lowercase tag name of containing element.
		* @param lcName Lowercase attribute name.
		* @param value Attribute value.
		* @return Returns true if `value` is valid, otherwise false.
		*/
		const _isValidAttribute = function _isValidAttribute(lcTag, lcName, value) {
			if (FORBID_ATTR[lcName]) return false;
			if (_isPatchLinkageAttribute(lcName, lcTag)) return false;
			if (SANITIZE_DOM && (lcName === "id" || lcName === "name") && (value in document || value in formElement)) return false;
			const nameIsPermitted = ALLOWED_ATTR[lcName] || EXTRA_ELEMENT_HANDLING.attributeCheck instanceof Function && EXTRA_ELEMENT_HANDLING.attributeCheck(lcName, lcTag);
			if (ALLOW_DATA_ATTR && regExpTest(DATA_ATTR$1, lcName)) return true;
			if (ALLOW_ARIA_ATTR && regExpTest(ARIA_ATTR$1, lcName)) return true;
			if (!nameIsPermitted) return _isBasicCustomElement(lcTag) && _matchesNameCheck(CUSTOM_ELEMENT_HANDLING.tagNameCheck, lcTag) && _matchesNameCheck(CUSTOM_ELEMENT_HANDLING.attributeNameCheck, lcName, lcTag) || lcName === "is" && CUSTOM_ELEMENT_HANDLING.allowCustomizedBuiltInElements && _matchesNameCheck(CUSTOM_ELEMENT_HANDLING.tagNameCheck, value);
			if (URI_SAFE_ATTRIBUTES[lcName]) return true;
			if (regExpTest(IS_ALLOWED_URI$1, stringReplace(value, ATTR_WHITESPACE$1, ""))) return true;
			if ((lcName === "src" || lcName === "xlink:href" || lcName === "href") && lcTag !== "script" && stringIndexOf(value, "data:") === 0 && DATA_URI_TAGS[lcTag]) return true;
			if (ALLOW_UNKNOWN_PROTOCOLS && !regExpTest(IS_SCRIPT_OR_DATA$1, stringReplace(value, ATTR_WHITESPACE$1, ""))) return true;
			return !value;
		};
		const RESERVED_CUSTOM_ELEMENT_NAMES = addToSet({}, [
			"annotation-xml",
			"color-profile",
			"font-face",
			"font-face-format",
			"font-face-name",
			"font-face-src",
			"font-face-uri",
			"missing-glyph"
		]);
		/**
		* _isBasicCustomElement
		* checks if at least one dash is included in tagName, and it's not the first char
		* for more sophisticated checking see https://github.com/sindresorhus/validate-element-name
		*
		* @param tagName name of the tag of the node to sanitize
		* @returns Returns true if the tag name meets the basic criteria for a custom element, otherwise false.
		*/
		const _isBasicCustomElement = function _isBasicCustomElement(tagName) {
			return !RESERVED_CUSTOM_ELEMENT_NAMES[stringToLowerCase(tagName)] && regExpTest(CUSTOM_ELEMENT$1, tagName);
		};
		/**
		* Wrap an attribute value in the matching Trusted Types object when
		* the active policy requires it. Namespaced attributes pass through
		* unchanged (no TT support yet, see
		* https://bugs.chromium.org/p/chromium/issues/detail?id=1305293).
		*
		* @param lcTag lowercase tag name of the containing element
		* @param lcName lowercase attribute name
		* @param namespaceURI the attribute's namespace, if any
		* @param value the attribute value to wrap
		* @return the value, wrapped when Trusted Types demand it
		*/
		const _applyTrustedTypesToAttribute = function _applyTrustedTypesToAttribute(lcTag, lcName, namespaceURI, value) {
			if (trustedTypesPolicy && typeof trustedTypes === "object" && typeof trustedTypes.getAttributeType === "function" && !namespaceURI) switch (trustedTypes.getAttributeType(lcTag, lcName)) {
				case "TrustedHTML": return _createTrustedHTML(value);
				case "TrustedScriptURL": return _createTrustedScriptURL(value);
			}
			return value;
		};
		/**
		* Write a modified attribute value back onto the element. On
		* success, re-probe for clobbering introduced by the new value and
		* remove the element when found; otherwise, when this writeback is the
		* recreate half of the SANITIZE_NAMED_PROPS remove-and-recreate, pop the
		* removal entry that path recorded so it does not show as removed. On
		* failure, remove the attribute instead.
		*
		* Returns true only on a clean write (the value was set and the new value
		* introduced no clobbering). The caller uses that, together with its own
		* knowledge of whether this attribute pushed a DOMPurify.removed record, to
		* decide whether to pop that record. The pop must happen ONLY for the
		* named-prop remove-and-recreate; popping on any other value change (trim,
		* template scrubbing, Trusted Types) would consume an unrelated _forceRemove
		* subtree-cleanup record and let that detached subtree keep a live event
		* handler through the IN_PLACE neutralization pass (SO-001).
		*
		* @param currentNode the element carrying the attribute
		* @param name the attribute name as present on the element
		* @param namespaceURI the attribute's namespace, if any
		* @param value the new attribute value
		* @return true if the value was written without introducing clobbering
		*/
		const _setAttributeValue = function _setAttributeValue(currentNode, name, namespaceURI, value) {
			try {
				if (namespaceURI) currentNode.setAttributeNS(namespaceURI, name, value);
				else currentNode.setAttribute(name, value);
				if (_isClobbered(currentNode)) {
					_forceRemove(currentNode);
					return false;
				}
				return true;
			} catch (_) {
				_removeAttribute(name, currentNode);
				return false;
			}
		};
		/**
		* _sanitizeAttributes
		*
		* @protect attributes
		* @protect nodeName
		* @protect removeAttribute
		* @protect setAttribute
		*
		* @param currentNode to sanitize
		* @param root the current walk root
		*/
		const _sanitizeAttributes = function _sanitizeAttributes(currentNode, root) {
			_executeHooks(hooks.beforeSanitizeAttributes, currentNode, null);
			if (_handleHookDetachedNode(currentNode, root)) return;
			const attributes = currentNode.attributes;
			if (!attributes || _isClobbered(currentNode)) return;
			ALLOWED_ATTR = _forkSharedAllowlist(hooks.uponSanitizeAttribute, ALLOWED_ATTR, DEFAULT_ALLOWED_ATTR, SET_CONFIG_ALLOWED_ATTR);
			const hookEvent = {
				attrName: "",
				attrValue: "",
				keepAttr: true,
				allowedAttributes: ALLOWED_ATTR,
				forceKeepAttr: void 0
			};
			let l = attributes.length;
			const lcTag = transformCaseFunc(currentNode.nodeName);
			while (l--) {
				const attr = attributes[l];
				const name = attr.name, namespaceURI = attr.namespaceURI, attrValue = attr.value;
				const lcName = transformCaseFunc(name);
				const initValue = attrValue;
				let value = name === "value" ? initValue : stringTrim(initValue);
				let recreatedNamedProp = false;
				hookEvent.attrName = lcName;
				hookEvent.attrValue = value;
				hookEvent.keepAttr = true;
				hookEvent.forceKeepAttr = void 0;
				_executeHooks(hooks.uponSanitizeAttribute, currentNode, hookEvent);
				value = hookEvent.attrValue;
				if (SANITIZE_NAMED_PROPS && (lcName === "id" || lcName === "name") && stringIndexOf(value, SANITIZE_NAMED_PROPS_PREFIX) !== 0) {
					_removeAttribute(name, currentNode, attr);
					value = SANITIZE_NAMED_PROPS_PREFIX + value;
					recreatedNamedProp = true;
				}
				if (SAFE_FOR_XML && regExpTest(/((--!?|])>)|<\/(style|script|title|xmp|textarea|noscript|iframe|noembed|noframes)/i, value)) {
					_removeAttribute(name, currentNode, attr);
					continue;
				}
				if (lcName === "attributename" && stringMatch(value, "href")) {
					_removeAttribute(name, currentNode, attr);
					continue;
				}
				if (hookEvent.forceKeepAttr) continue;
				if (!hookEvent.keepAttr) {
					_removeAttribute(name, currentNode, attr);
					continue;
				}
				if (!ALLOW_SELF_CLOSE_IN_ATTR && regExpTest(SELF_CLOSING_TAG, value)) {
					_removeAttribute(name, currentNode, attr);
					continue;
				}
				if (SAFE_FOR_TEMPLATES) value = _stripTemplateExpressions(value);
				if (!_isValidAttribute(lcTag, lcName, value)) {
					_removeAttribute(name, currentNode, attr);
					continue;
				}
				value = _applyTrustedTypesToAttribute(lcTag, lcName, namespaceURI, value);
				if (value !== initValue) {
					if (_setAttributeValue(currentNode, name, namespaceURI, value) && recreatedNamedProp) arrayPop(DOMPurify.removed);
				}
			}
			_executeHooks(hooks.afterSanitizeAttributes, currentNode, null);
			_handleHookDetachedNode(currentNode, root);
		};
		/**
		* _sanitizeShadowDOM
		*
		* @param fragment to iterate over recursively
		*/
		const _sanitizeShadowDOM2 = function _sanitizeShadowDOM(fragment) {
			let shadowNode = null;
			const shadowIterator = _createNodeIterator(fragment);
			_executeHooks(hooks.beforeSanitizeShadowDOM, fragment, null);
			while (shadowNode = shadowIterator.nextNode()) {
				_executeHooks(hooks.uponSanitizeShadowNode, shadowNode, null);
				_sanitizeElements(shadowNode, fragment);
				_sanitizeAttributes(shadowNode, fragment);
				if (_isDocumentFragment(shadowNode.content)) _sanitizeShadowDOM2(shadowNode.content);
				if (_readNodeType(shadowNode) === NODE_TYPE.element) {
					const innerSr = getShadowRoot(shadowNode);
					if (_isDocumentFragment(innerSr)) {
						_sanitizeAttachedShadowRoots(innerSr);
						_sanitizeShadowDOM2(innerSr);
					}
				}
			}
			_executeHooks(hooks.afterSanitizeShadowDOM, fragment, null);
		};
		/**
		* _sanitizeAttachedShadowRoots
		*
		* Walks `root` and feeds every attached shadow root we encounter into
		* the existing _sanitizeShadowDOM pipeline. The default node iterator
		* does not descend into shadow trees, so nodes inside an attached
		* shadow root would otherwise be skipped entirely.
		*
		* Two real input paths put attached shadow roots in front of us:
		*   1. IN_PLACE on a DOM node that already has shadow roots attached.
		*   2. DOM-node input where importNode(dirty, true) deep-clones the
		*      shadow root because it was created with `clonable: true`.
		*
		* This pass runs once, up front, so the main iteration loop (and the
		* existing _sanitizeShadowDOM template-content recursion) stay
		* untouched — string-input paths are not affected.
		*
		* @param root the subtree root to walk for attached shadow roots
		*/
		const _sanitizeAttachedShadowRoots = function _sanitizeAttachedShadowRoots(root) {
			const stack = [{
				node: root,
				shadow: null
			}];
			while (stack.length > 0) {
				const item = stack.pop();
				if (item.shadow) {
					_sanitizeShadowDOM2(item.shadow);
					continue;
				}
				const node = item.node;
				const isElement = _readNodeType(node) === NODE_TYPE.element;
				const childNodes = getChildNodes(node);
				if (childNodes) for (let i = childNodes.length - 1; i >= 0; --i) stack.push({
					node: childNodes[i],
					shadow: null
				});
				if (isElement) {
					const rootName = getNodeName ? getNodeName(node) : null;
					if (typeof rootName === "string" && transformCaseFunc(rootName) === "template") {
						const content = node.content;
						if (_isDocumentFragment(content)) stack.push({
							node: content,
							shadow: null
						});
					}
				}
				if (isElement) {
					const sr = getShadowRoot(node);
					if (_isDocumentFragment(sr)) stack.push({
						node: null,
						shadow: sr
					}, {
						node: sr,
						shadow: null
					});
				}
			}
		};
		DOMPurify.sanitize = function(dirty) {
			let cfg = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {};
			let body = null;
			let importedNode = null;
			let currentNode = null;
			let returnNode = null;
			IS_EMPTY_INPUT = !dirty;
			if (IS_EMPTY_INPUT) dirty = "<!-->";
			if (typeof dirty !== "string" && !_isNode(dirty)) {
				dirty = stringifyValue(dirty);
				if (typeof dirty !== "string") throw typeErrorCreate("dirty is not a string, aborting");
			}
			if (!DOMPurify.isSupported) return dirty;
			if (SET_CONFIG) {
				ALLOWED_TAGS = SET_CONFIG_ALLOWED_TAGS;
				ALLOWED_ATTR = SET_CONFIG_ALLOWED_ATTR;
			} else _parseConfig(cfg);
			if (hooks.uponSanitizeElement.length > 0 || hooks.uponSanitizeAttribute.length > 0) ALLOWED_TAGS = clone(ALLOWED_TAGS);
			if (hooks.uponSanitizeAttribute.length > 0) ALLOWED_ATTR = clone(ALLOWED_ATTR);
			DOMPurify.removed = [];
			const inPlace = IN_PLACE && typeof dirty !== "string" && _isNode(dirty);
			if (inPlace) {
				_neutralizePatchLinkage(dirty);
				const nn = _readNodeName(dirty);
				if (typeof nn === "string") {
					const tagName = transformCaseFunc(nn);
					if (!ALLOWED_TAGS[tagName] || FORBID_TAGS[tagName]) {
						_neutralizeRoot(dirty);
						throw typeErrorCreate("root node is forbidden and cannot be sanitized in-place");
					}
				}
				if (_isClobbered(dirty)) {
					_neutralizeRoot(dirty);
					throw typeErrorCreate("root node is clobbered and cannot be sanitized in-place");
				}
				try {
					_sanitizeAttachedShadowRoots(dirty);
				} catch (error) {
					_neutralizeRoot(dirty);
					throw error;
				}
			} else if (_isNode(dirty)) {
				body = _initDocument("<!---->");
				importedNode = body.ownerDocument.importNode(dirty, true);
				if (importedNode.nodeType === NODE_TYPE.element && importedNode.nodeName === "BODY") body = importedNode;
				else if (importedNode.nodeName === "HTML") body = importedNode;
				else body.appendChild(importedNode);
				_sanitizeAttachedShadowRoots(body);
			} else {
				if (!RETURN_DOM && !SAFE_FOR_TEMPLATES && !WHOLE_DOCUMENT && dirty.indexOf("<") === -1) return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? _createTrustedHTML(dirty) : dirty;
				body = _initDocument(dirty);
				if (!body) return RETURN_DOM ? null : RETURN_TRUSTED_TYPE ? emptyHTML : "";
			}
			if (body && FORCE_BODY) _forceRemove(body.firstChild);
			const walkRoot = inPlace ? dirty : body;
			try {
				const nodeIterator = _createNodeIterator(walkRoot);
				while (currentNode = nodeIterator.nextNode()) {
					_sanitizeElements(currentNode, walkRoot);
					_sanitizeAttributes(currentNode, walkRoot);
					if (_isDocumentFragment(currentNode.content)) _sanitizeShadowDOM2(currentNode.content);
				}
			} catch (error) {
				if (inPlace) {
					_neutralizeRoot(dirty);
					arrayForEach(DOMPurify.removed, (entry) => {
						if (entry.element) _neutralizeSubtree(entry.element);
					});
				}
				throw error;
			}
			if (inPlace) {
				let rootWasRemoved = false;
				arrayForEach(DOMPurify.removed, (entry) => {
					if (entry.element) {
						if (entry.element === dirty) rootWasRemoved = true;
						_neutralizeSubtree(entry.element);
					}
				});
				if (rootWasRemoved) throw typeErrorCreate("a node selected for removal could not be safely returned; refusing to sanitize in place");
				if (SAFE_FOR_TEMPLATES) _scrubTemplateExpressions2(dirty);
				return dirty;
			}
			if (RETURN_DOM) {
				if (SAFE_FOR_TEMPLATES) _scrubTemplateExpressions2(body);
				if (RETURN_DOM_FRAGMENT) {
					returnNode = createDocumentFragment.call(body.ownerDocument);
					while (body.firstChild) returnNode.appendChild(body.firstChild);
				} else returnNode = body;
				if (ALLOWED_ATTR.shadowroot || ALLOWED_ATTR.shadowrootmode) returnNode = importNode.call(originalDocument, returnNode, true);
				return returnNode;
			}
			let serializedHTML = WHOLE_DOCUMENT ? body.outerHTML : body.innerHTML;
			if (WHOLE_DOCUMENT && ALLOWED_TAGS["!doctype"] && body.ownerDocument && body.ownerDocument.doctype && body.ownerDocument.doctype.name && regExpTest(DOCTYPE_NAME, body.ownerDocument.doctype.name)) serializedHTML = "<!DOCTYPE " + body.ownerDocument.doctype.name + ">\n" + serializedHTML;
			if (SAFE_FOR_TEMPLATES) serializedHTML = _stripTemplateExpressions(serializedHTML);
			return trustedTypesPolicy && RETURN_TRUSTED_TYPE ? _createTrustedHTML(serializedHTML) : serializedHTML;
		};
		DOMPurify.setConfig = function() {
			let cfg = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {};
			_parseConfig(cfg);
			SET_CONFIG = true;
			SET_CONFIG_ALLOWED_TAGS = ALLOWED_TAGS;
			SET_CONFIG_ALLOWED_ATTR = ALLOWED_ATTR;
		};
		DOMPurify.clearConfig = function() {
			CONFIG = null;
			SET_CONFIG = false;
			SET_CONFIG_ALLOWED_TAGS = null;
			SET_CONFIG_ALLOWED_ATTR = null;
			trustedTypesPolicy = defaultTrustedTypesPolicy;
			emptyHTML = "";
		};
		DOMPurify.isValidAttribute = function(tag, attr, value) {
			if (!CONFIG) _parseConfig({});
			const lcTag = transformCaseFunc(tag);
			const lcName = transformCaseFunc(attr);
			return _isValidAttribute(lcTag, lcName, value);
		};
		DOMPurify.addHook = function(entryPoint, hookFunction) {
			if (typeof hookFunction !== "function") return;
			if (!objectHasOwnProperty(hooks, entryPoint)) return;
			arrayPush(hooks[entryPoint], hookFunction);
		};
		DOMPurify.removeHook = function(entryPoint, hookFunction) {
			if (!objectHasOwnProperty(hooks, entryPoint)) return;
			if (hookFunction !== void 0) {
				const index = arrayLastIndexOf(hooks[entryPoint], hookFunction);
				return index === -1 ? void 0 : arraySplice(hooks[entryPoint], index, 1)[0];
			}
			return arrayPop(hooks[entryPoint]);
		};
		DOMPurify.removeHooks = function(entryPoint) {
			if (!objectHasOwnProperty(hooks, entryPoint)) return;
			hooks[entryPoint] = [];
		};
		DOMPurify.removeAllHooks = function() {
			hooks = _createHooksMap();
		};
		return DOMPurify;
	}
	var purify = createDOMPurify();

	//# sourceMappingURL=purify.es.mjs.map

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

		image: // Kunena change in html code 
			'<div><label for="image">{url}</label> ' +
				'<input type="text" id="image" dir="ltr" placeholder="https://" /></div>' +
		            '<div><center><i><label for="properties">optional</label></i></center><br> ' +
		            '<div><label for="altxt">{altxt}</label> ' +
						'<input type="text" id="altxt" size="20" dir="ltr" /></div>' +
					'<div><label for="size">{width}&emsp;{height}&emsp;{float}</label> ' +
					'<input type="text" id="width" size="3" dir="ltr" />&emsp;<input type="text" id="height" size="3" dir="ltr" />&emsp;&nbsp;' +
		                '<select name="float" id="float">' +
							'<option value="">' + '' + '</option>' +
							'<option value="left">' + 'left' + '</option>' +
							'<option value="right">' + 'right' + '</option>' +
						'</select></div>' +
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
	 * @param {boolean} [sanitize=true]
	 * @returns {string|DocumentFragment}
	 * @private
	 */
	function _tmpl (name, params, createHtml, sanitize) {
		var template = _templates[name];

		Object.keys(params).forEach(function (name) {
			// Default to sanitizing
			if (sanitize !== false) {
				params[name] = entities(String(params[name]));
			}

			template = template.replace(
				new RegExp(regex('{' + name + '}'), 'g'), params[name]
			);
		});

		// Default to sanitizing
		if (sanitize !== false) {
			template = purify.sanitize(template, {ADD_ATTR: ['unselectable']});
		}

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
							// Only allow named, #aaa, hsl(1.1 50% / 1), etc.
							if (!/^[\#a-z0-9\-\(\) \/%\.]+$/i.test(color)) {
								color = '';
							}

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

		// START_COMMAND: Kunena change on Horizontal Rule
		horizontalrule: {
			exec: function () {
				this.wysiwygEditorInsertHtml(
					'<br>',
					'<hr>'
				);
			},
			tooltip: Joomla.Text._('COM_KUNENA_EDITOR_HR')
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
					// Kunena change : add float and altxt, and add language string on insert
					float: editor._('Position:'),
					altxt: editor._('ALT (alternativ Text):'),  
					insert: editor._(Joomla.Text._('COM_KUNENA_SCEDITOR_BUTTON_INSERT_LABEL'))
				}, true));

				var	urlInput = find(content, '#image')[0];

				// Kunena change
				if (selected.url) {  //if an image is selected
					urlInput.value = selected.url;
					find(content, '#width')[0].value = selected.width;
					find(content, '#height')[0].value = selected.height;
					find(content, '#float')[0].value = selected.float;  
				    find(content, '#altxt')[0].value = selected.alt;                
				}
				else {urlInput.value = '';}            

				on(content, 'click', '.button', function (e) {   
					if (urlInput.value) {  //only if url is set                  
						cb(
							urlInput.value,
							find(content, '#width')[0].value,
							find(content, '#height')[0].value,
							find(content, '#float')[0].value,
							find(content, '#altxt')[0].value                                                      
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
					selected,
					function (url, width, height, float, altxt) {
						// Kunena change : variable definition added
						var attrs  = ' tabindex="-1"'; 

						if (width) {
							attrs += ' width="' + parseInt(width, 10) + '"';
						}

						if (height) {
							attrs += ' height="' + parseInt(height, 10) + '"';
						}
						
						if (float) {
							attrs += ' style="float:' + float + '"';
						} 

						if (altxt) {
							attrs += ' alt="' + altxt + '"';
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
							src: uriScheme(
								emoticonsRoot + (emoticon.url || emoticon)),
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
					// Set sanatise to false as sanatisaion is handled by
					//  wysiwygEditorInsertHtml()
					editor.wysiwygEditorInsertHtml(_tmpl('youtube', {
						id: id,
						time: time
					}, false, false));
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
			var previousText = (prev && prev.nodeValue) || '';

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
			remove(emoticon);
			if (prev) {
				remove(prev);
			}

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
			const allowedTags = ['iframe'].concat(options.allowedTags);
			const allowedAttrs = ['allowfullscreen', 'frameborder', 'target']
				.concat(options.allowedAttributes);

			return domPurify.sanitize(html, {
				ADD_TAGS: allowedTags,
				ADD_ATTR: allowedAttrs
			});
		}

		// eslint-disable-next-line camelcase
		base.__UNSTABLE__sanitize = sanitize;

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
			
			// Kunena change
			wysiwygEditor.onload = function() {   //add tabindex to all images to get focus attribute
				wysiwygEditor.contentWindow.document.querySelectorAll('img').forEach(img => {
			               img.setAttribute('tabindex','-1'); });              
			};

			// TODO: make this optional somehow
			base.dimensions(
				options.width || width(original),
				options.height || height(original)
			);

			if (!domPurify.isValidAttribute('link', 'href', options.style)) {
				options.style = '';
			}

			if (!/^[a-z\-0-9 ]+$/i.test(options.charset)) {
				options.charset = 'UTF-8';
			}

			wysiwygDocument = wysiwygEditor.contentDocument;
			wysiwygDocument.open();
			wysiwygDocument.write(_tmpl('html', {
				// Add ios to HTML so can apply CSS fix to only it
				attrs: ios ? ' class="ios"' : '',
				spellcheck: options.spellcheck ? '' : 'spellcheck="false"',
				charset: options.charset,
				style: entities(options.style)
			}, false, false));
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
						src: uriScheme(root + (url.url || url))
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
				return sourceEditor.readOnly;
			}

			wysiwygBody.contentEditable = !readOnly;
			sourceEditor.readOnly = readOnly;

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

			wrapInlines(wysiwygBody, wysiwygDocument);

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
			
			// Kunena change
			wysiwygEditor.contentWindow.document.querySelectorAll('img').forEach(img => {
				img.setAttribute('tabindex', '-1');
			});
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
				// Kunena
				if (caller.classList.contains('sceditor-button-image') && !isUndefined(wysiwygEditor.contentWindow)) {                
					var selectitem = wysiwygEditor.contentWindow.document.querySelectorAll(':focus').item(0);                   
						if (!isUndefined(selectitem) && selectitem && selectitem.tagName === 'IMG'){ 
				        	selected.url = selectitem.src;
				            selected.width = selectitem.width;
				            selected.height = selectitem.height;
				            selected.float = selectitem.style.cssFloat;
				            selected.alt = selectitem.alt;                            
					}
				}
				
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
			// eslint-disable-next-line no-unused-vars
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
							// eslint-disable-next-line no-unused-vars
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
					';': ':',
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

})();
