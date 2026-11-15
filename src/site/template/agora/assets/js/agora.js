/**
 * Kunena Component
 * @package Kunena.Template.Agora
 *
 * @copyright     Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

"use strict";

document.addEventListener("DOMContentLoaded", function () {
	const forum = document.getElementById("kunena");

	if (!forum || !forum.classList.contains("agora")) {
		return;
	}

	/* The text of SCEditor and of CKEditor is in a frame with its own style sheet (dark text on white): it gets the colours of the
	 * forum. */
	const frames = ".sceditor-container iframe, .cke_wysiwyg_frame";

	const paintEditors = function () {
		const colors = getComputedStyle(forum);
		const text = colors.getPropertyValue("--agora-text").trim();
		const link = colors.getPropertyValue("--agora-link").trim();
		const surface = colors.getPropertyValue("--agora-surface").trim();

		forum.querySelectorAll(frames).forEach(function (frame) {
			const doc = frame.contentDocument;

			if (!doc || !doc.head) {
				return;
			}

			let style = doc.getElementById("agora-editor-colors");

			if (!style) {
				style = doc.createElement("style");
				style.id = "agora-editor-colors";
				doc.head.appendChild(style);
			}

			style.textContent = "html, body, body.cke_editable { color: " + text + "; background: " + surface + "; }"
				+ " p, table, code::before { color: " + text + "; }"
				+ " a { color: " + link + "; }"
				+ " blockquote, code, pre { color: inherit; background: rgba(127, 127, 127, 0.12); }";
		});
	};

	/* The editors are made after this script, and SCEditor writes the document of its frame after it put the frame in the page: the
	 * frames are painted when they appear, when they are loaded and a few times after. */
	const watchEditors = function () {
		forum.querySelectorAll(".sceditor-container iframe:not([data-agora-painted]), .cke_wysiwyg_frame:not([data-agora-painted])").forEach(function (frame) {
			frame.setAttribute("data-agora-painted", "1");
			frame.addEventListener("load", paintEditors);
		});

		[0, 200, 800, 2000].forEach(function (delay) {
			setTimeout(paintEditors, delay);
		});
	};

	if (window.MutationObserver) {
		new MutationObserver(function (mutations) {
			const added = mutations.some(function (mutation) {
				return Array.prototype.some.call(mutation.addedNodes, function (node) {
					return node.nodeType === 1 && (node.matches("iframe, .sceditor-container") || node.querySelector("iframe"));
				});
			});

			if (added) {
				watchEditors();
			}
		}).observe(forum, { childList: true, subtree: true });
	}

	window.addEventListener("load", watchEditors);

	/* CKEditor writes the document of its frame again (when it gets its text, after the source mode): painted each time. */
	if (window.CKEDITOR) {
		const hook = function (editor) {
			editor.on("contentDom", paintEditors);
		};

		Object.keys(window.CKEDITOR.instances).forEach(function (name) {
			hook(window.CKEDITOR.instances[name]);
		});

		window.CKEDITOR.on("instanceCreated", function (event) {
			hook(event.editor);
		});
	}

	/* The colour scheme auto of the options follows the site template around the forum (window.kunenaAgoraTheme() of the script in
	 * #kunena: the data-bs-theme of the page, else its background): the forum (the scheme of Bootstrap, data-bs-theme, of #kunena)
	 * and the pop-ups which Bootstrap puts at the end of the page get the light or the dark colours, also when the site template
	 * changes them (data-bs-theme of html or body, or its own colours after a change of the colours of the device). */
	if (forum.dataset.agoraScheme !== "auto" || typeof window.kunenaAgoraTheme !== "function") {
		return;
	}

	const apply = function () {
		const theme = window.kunenaAgoraTheme(forum);

		forum.setAttribute("data-bs-theme", theme);

		document.querySelectorAll("body > .tooltip, body > .popover, body > .modal").forEach(function (el) {
			el.setAttribute("data-bs-theme", theme);
		});

		paintEditors();
	};

	/* After the handlers of the site template, which can follow the device too. */
	window.matchMedia("(prefers-color-scheme: dark)").addEventListener("change", function () {
		setTimeout(apply, 0);
	});

	if (window.MutationObserver) {
		const observer = new MutationObserver(apply);

		[document.documentElement, document.body].forEach(function (el) {
			observer.observe(el, { attributes: true, attributeFilter: ["data-bs-theme"] });
		});
	}

	apply();
});
