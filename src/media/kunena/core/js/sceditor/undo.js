(function (sceditor) {
	'use strict';

	sceditor.plugins.undo = function () {
		var base = this;
        var utils = sceditor.utils;
		var sourceEditor;
		var editor;
		var body;
		var lastInputType = '';
		var charChangedCount = 0;
		var isInPatchedFn = false;
		/**
		 * If currently restoring a state
		 * Should ignore events while it's happening
		 */
		var isApplying = false;
		/**
		 * If current selection change event has already been stored
		 */
		var isSelectionChangeHandled = false;

		var undoLimit  = 50;
		var undoStates = [];
		var redoPosition = 0;
		var lastState;

		/**
		 * Sets the editor to the specified state.
		 * @param  {Object} state
		 * @private
		 */
		function applyState(state) {
			isApplying = true;
			editor.sourceMode(state.sourceMode);

			if (state.sourceMode) {
				editor.val(state.value, false);
				editor.sourceEditorCaret(state.caret);
			} else {
				editor.getBody().innerHTML = state.value;

				// Caret may not exist for the first state in Firefox
				if (state.caret) {
					var range = editor.getRangeHelper().selectedRange();
					setRangePositions(range, state.caret);
					editor.getRangeHelper().selectRange(range);
				}
			}

			editor.focus();
			isApplying = false;
		};

		/**
		 * Patches a function on the object to call store() after invocation
		 * @param {Object} obj
		 * @param {string} fn
		 */
		function patch(obj, fn) {
			var origFn = obj[fn];
			obj[fn] = function () {
				// sourceMode calls other patched methods so need to ignore them
				var ignore = isInPatchedFn;

				// Store caret position before any change is made
				if (!ignore && !isApplying && lastState &&
						editor.getRangeHelper().hasSelection()) {
					updateLastState();
				}

				isInPatchedFn = true;
				origFn.apply(this, arguments);

				if (!ignore) {
					isInPatchedFn = false;

					if (!isApplying) {
						storeState();
						lastInputType = '';
					}
				}
			};
		}

		/**
		 * Stores the editors current state
		 */
		function storeState() {
			if (redoPosition) {
				undoStates.length -= redoPosition;
				redoPosition = 0;
			}

			if (undoLimit > 0 && undoStates.length > undoLimit) {
				undoStates.shift();
			}

			lastState = {};
			updateLastState();
			undoStates.push(lastState);
		}

		/**
		 * Updates the last saved state with the editors current state
		 */
		function updateLastState() {
			var sourceMode = editor.sourceMode();
			lastState.caret = sourceMode ? editor.sourceEditorCaret() :
				getRangePositions(editor.getRangeHelper().selectedRange());
			lastState.sourceMode = sourceMode;
			lastState.value = sourceMode ?
				editor.getSourceEditorValue(false) :
				editor.getBody().innerHTML;
		}

		base.init = function () {
			// The this variable will be set to the instance of the editor
			// calling it, hence why the plugins "this" is saved to the base
			// variable.
			editor = this;

			undoLimit = editor.undoLimit || undoLimit;

			editor.addShortcut('ctrl+z', base.undo);
			editor.addShortcut('ctrl+shift+z', base.redo);
			editor.addShortcut('ctrl+y', base.redo);

          editor.clearUndo = function () {
				undoStates = [];
				redoPosition = 0;
				storeState();
			};
          
        var commands = this.commands;
        commands.undo = utils.extend(commands.undo || {}, {
 		state: function () {
 			return redoPosition < undoStates.length - 1 ? 0 : -1;
 		},
 		exec: function () {
 			base.undo();
 		},
 		tooltip: 'Undo'
 	    });

 	    commands.redo = utils.extend(commands.redo || {}, {
 		state: function () {
 			return redoPosition > 0 ? 0 : -1;
 		},
 		exec: function () {
 			base.redo();
 		},
 		tooltip: 'Redo'
 	    });          
		};

		function documentSelectionChangeHandler() {
			if (sourceEditor === document.activeElement) {
				base.signalSelectionchangedEvent();
			}
		}

		base.signalReady = function () {
			sourceEditor = editor.getContentAreaContainer().nextSibling;
			body = editor.getBody();

			// Store initial state
			storeState();

			// Patch methods that allow inserting content into the editor
			// programmatically
			// TODO: remove this when there is a built in event to handle it
			patch(editor, 'setWysiwygEditorValue');
			patch(editor, 'setSourceEditorValue');
			patch(editor, 'sourceEditorInsertText');
			patch(editor.getRangeHelper(), 'insertNode');
			patch(editor, 'toggleSourceMode');

			/**
			 * Handles the before input event so can override built in
			 * undo / redo
			 * @param {InputEvent} e
			 */
			function beforeInputHandler(e) {
				if (e.inputType === 'historyUndo') {
					base.undo();
					e.preventDefault();
				} else if (e.inputType === 'historyRedo') {
					base.redo();
					e.preventDefault();
				}
			}

			body.addEventListener('beforeinput', beforeInputHandler);
			sourceEditor.addEventListener('beforeinput', beforeInputHandler);

			/**
			 * Should always store state at the end of composing
			 */
			function compositionHandler() {
				lastInputType = '';
				storeState();
			}
			body.addEventListener('compositionend', compositionHandler);
			sourceEditor.addEventListener('compositionend', compositionHandler);

			// Chrome doesn't trigger selectionchange on textarea so need to
			// listen to global event
			document.addEventListener('selectionchange',
				documentSelectionChangeHandler);
		};

		base.destroy = function () {
			document.removeEventListener('selectionchange',
				documentSelectionChangeHandler);
		};

		base.undo = function () {
			lastState = null;

			if (redoPosition < undoStates.length - 1) {
				redoPosition++;
				applyState(undoStates[undoStates.length - 1 - redoPosition]);
			}

			return false;
		};

		base.redo = function () {
			if (redoPosition > 0) {
				redoPosition--;
				applyState(undoStates[undoStates.length - 1 - redoPosition]);
			}

			return false;
		};

		/**
		 * Handle the selectionchanged event so can store the last caret
		 * position before the input so undoing places it in the right place
		 */
		base.signalSelectionchangedEvent = function () {
			if (isApplying || isSelectionChangeHandled) {
				isSelectionChangeHandled = false;
				return;
			}
			if (lastState) {
				updateLastState();
			}
			lastInputType = '';
		};

		/**
		 * Handles the input event
		 * @param {InputEvent} e
		 */
		base.signalInputEvent = function (e) {
			// InputType is one of
			// https://rawgit.com/w3c/input-events/v1/index.html#interface-InputEvent-Attributes
			// Most should cause a full undo item to be added so only need to
			// handle a few of them
			var inputType = e.inputType;

			// Should ignore selection changes that occur because of input
			// events as already handling them
			isSelectionChangeHandled = true;

			// inputType should be supported by all supported browsers
			// except IE 11 in runWithoutWysiwygSupport. Shouldn't be an issue
			// as native handling will mostly work there.
			// Ignore if composing as will handle composition end instead
			if (!inputType || e.isComposing) {
				return;
			}

			switch (e.inputType) {
				case 'deleteContentBackward':
					if (lastState && lastInputType === inputType &&
						charChangedCount < 20) {
						updateLastState();
					} else {
						storeState();
						charChangedCount = 0;
					}

					lastInputType = inputType;
					break;

				case 'insertText':
					charChangedCount += e.data ? e.data.length : 1;

					if (lastState && lastInputType === inputType &&
							charChangedCount < 20 && !/\s$/.test(e.data)) {
						updateLastState();
					} else {
						storeState();
						charChangedCount = 0;
					}

					lastInputType = inputType;
					break;
				default:
					lastInputType = 'sce-misc';
					charChangedCount = 0;
					storeState();
					break;
			}
		};

		/**
		 * Creates a positions object form passed range
		 * @param {Range} range
		 * @return {Object<string, Array<number>}
		 */
		function getRangePositions(range) {
			// In Firefox, range may not exist when the editor is first created
			// due to Firefox returning null from getSelection() when the
			// editors iframe is first created. See issue #910
			if (!range) {
				return;
			}

			// Merge any adjacent text nodes as it will be done by innerHTML
			// which would cause positions to be off if not done
			body.normalize();

			return {
				startPositions:
					nodeToPositions(range.startContainer, range.startOffset),
				endPositions:
					nodeToPositions(range.endContainer, range.endOffset)
			};
		}

		/**
		 * Sets the range start/end based on the positions object
		 * @param {Range} range
		 * @param {Object<string, Array<number>>} positions
		 */
		function setRangePositions(range, positions) {
			try {
				var startPositions = positions.startPositions;
				var endPositions = positions.endPositions;

				range.setStart(positionsToNode(body, startPositions),
					startPositions[0]);
				range.setEnd(positionsToNode(body, endPositions),
					endPositions[0]);
			} catch (e) {
				if (console && console.warn) {
					console.warn('[SCEditor] Undo plugin lost caret', e);
				}
			}
		}

		/**
		 * Converts the passed container and offset into positions array
		 * @param {Node} container
		 * @param {number} offset
		 * @returns {Array<number>}
		 */
		function nodeToPositions(container, offset) {
			var positions = [offset];
			var node = container;

			while (node && node.tagName !== 'BODY') {
				positions.push(nodeIndex(node));
				node = node.parentNode;
			}

			return positions;
		}

		/**
		 * Returns index of passed node
		 * @param {Node} node
		 * @returns {number}
		 */
		function nodeIndex(node) {
			var i = 0;
			while ((node = node.previousSibling)) {
				i++;
			}
			return i;
		}

		/**
		 * Gets the container node from the positions array
		 * @param {Node} node
		 * @param {Array<number>} positions
		 * @returns {Node}
		 */
		function positionsToNode(node, positions) {
			for (var i = positions.length - 1; node && i > 0; i--) {
				node = node.childNodes[positions[i]];
			}
			return node;
		}

        //function to add buttons
     /*   jQuery(document).ready(function ($) {
          $(function(){
            if(!$('.sceditor-button-undo').length) {  //would load 2 times (1=textarea.basic + 2=textarea.private)
            var undoimg = encodeURI('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 23 23"><path fill="FireBrick" d="M12.5 8c4.65 0 8.58 3.03 9.97 7.22L20.1 16c-1.05-3.19-4.06-5.5-7.6-5.5-1.96 0-3.73.72-5.12 1.88L11 16H2V7l3.6 3.6C7.45 9 9.85 8 12.5 8Z" /></svg>');
            $('<a class="sceditor-button sceditor-button-undo" unselectable="on" title="Undo [CTRL+z]" value="CTRL+Z"><div unselectable="on" style="background-image:url(\'' + undoimg + '\')">undo</div></a>').insertBefore('.sceditor-button-cut').click(function(){
            base.undo();
            // "background-image:url(/media/kunena/core/images/undo.png)"
            });
            var redoimg = encodeURI('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 23 23"><path fill="DodgerBlue" d="M18.4 10.6C16.55 9 14.15 8 11.5 8c-4.65 0-8.58 3.03-9.96 7.22L3.9 16c1.05-3.19 4.05-5.5 7.6-5.5 1.95 0 3.73.72 5.12 1.88L13 16h9V7l-3.6 3.6Z" /></svg>');
            $('<a class="sceditor-button sceditor-button-redo" unselectable="on" title="Redo [CTRL+y]" value="CTRL+y"><div unselectable="on" style="background-image:url(\'' + redoimg + '\')">redo</div></a>').insertBefore('.sceditor-button-cut').click(function(){
            base.redo();
            // "background-image:url(/media/kunena/core/images/redo.png)"
            });
            }
          });
        }); */
      
	};
}(sceditor));
