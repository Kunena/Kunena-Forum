/* ==========================================================
 * bootstrap-alert.js v2.0.4
 * http://twitter.github.com/bootstrap/javascript.html#alerts
 * ==========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Modified for MooTools by GP Technology Solutions Pty Ltd
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

Element.implement ({
    alert: function(options) {
        if ( this.retrieve('alert') === null ) {
            this.store('alert', new Alert (options, this));
        }
        return this.retrieve('alert');
    }
});

Alert = new Class({
    Implements: [Options, Events],
    options: {
        animation: true
    },

    initialize: function (options, selector) {
        this.selector = selector;

        this.setOptions(options);
        this.setOptions(this.getDataOptions(selector));

        if (!this.options.target) {

            this.target = selector.getParent('.alert');

        } else {

            this.target = this.options.target;

        }
    },

    close: function (e) {
        e && e.preventDefault(); // If e is event, prevent default action

        this.target.fireEvent('close', new Event.Mock(this.target, 'close'));

        if (this.options.animation) {

            this.target.fadeAndDestroy();

        } else {

            this.target.hide().dispose();

        }
    },

    /**
     * Get Options set on the Element via the dataset tags, data-animation etc.
     * @return object Key Value pair object of dataset tags.
     */
    getDataOptions: function (selector) {
        var dataset_name;
        var dataset_value;
        var options = {};
        var element = selector;

        if (typeof element.dataset != 'undefined') {

            for (dataset_name in element.dataset) {

                dataset_value = this.trueValue( element.dataset[dataset_name] );

                options[dataset_name] = dataset_value;
            }

            return options;

        } else if (Browser.ie) {

            // Cycle through options name to find data-<name> values where dataset is not available to us.
            for (dataset_name in this.options) {

                if (element.get('data-' + dataset_name)) {

                    options[dataset_name] = this.trueValue( element.get('data-' + dataset_name) );

                }

            }

            return options;

        } else {

            // Can't find data options, return empty object
            return {};

        }
    },

    /**
     * trueValue convert strings to their literals.
     * @param  string value String Value to convert to literal
     * @return mixed        Literal value of string where applicable, or the string.
     */
    trueValue: function (value) {
        if (value == 'true') {
            return true;
        } else if (value == 'false') {
            return false;
        } else if (value == 'null') {
            return null;
        } else {
            return value;
        }
    }
});

window.addEvent('domready', function() {
    $(document.body).getElements('.alert').each(function (alert_element) {
        if (alert_element.hasClass('fade')) {
            var timeout_fade_in = setTimeout(function () {
                alert_element.addClass('in');
            },300);
        }
    });

    $(document.body).addEvent('click:relay([data-dismiss=alert])', function() {
        this.alert().close();
    });
});


/**
 * http://davidwalsh.name/mootools-event
 * creates a Mock event to be used with fire event
 * @param Element target an element to set as the target of the event - not required
 *  @param string type the type of the event to be fired. Will not be used by IE - not required.
 *
 */
Event.Mock = function(target,type){
    var e = window.event;
    type = type || 'click';

    if (document.createEvent){
        e = document.createEvent('HTMLEvents');
        e.initEvent(
          type, //event type
          false, //bubbles - set to false because the event should like normal fireEvent
          true //cancelable
        );
    }
    e = new Event(e);
    e.target = target;
    return e;
};

Element.implement({
    fadeAndDestroy: function(duration) {
        var el = this;
        duration = duration || 600;
        this.set('tween', {
            duration: duration
        }).fade('out').get('tween').chain(function() {
            el.dispose();
        });
    }
});
/* ============================================================
 * bootstrap-dropdown.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#dropdowns
 * ============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */

 Element.implement ({
    dropdown: function () {
        if (this.retrieve('dropdown') === null) {
            this.store('dropdown', new Dropdown (this));
        }
        return this.retrieve('dropdown');
    }
 });

 Dropdown = new Class({
    initialize: function (element) {
        this.element = element;

        // Dropdown menu with class of .dropdown-menu SHOULD be the be a next sibling.
        this.dropdown_menu = this.element.getNext('.dropdown-menu');


        // if it's not the next sibling, fall back to a previous sibling.
        if (!this.dropdown_menu) {
            this.dropdown_menu = this.element.getPrevious('.dropdown-menu');
        }

        // Well it's not a next or previous sibling, perhaps for unknown reasons he's a child.
        if (!this.dropdown_menu) {
            this.dropdown_menu = this.element.getElement('.dropdown-menu');
        }

        // And now we give up. Cause hey, what's going on here. (Someone going to read the comments I wonder?)  =D
        if (!this.dropdown_menu) {
            return;
        }

        this.touchStart = false;


        $(document.body).addEvent('click', function (event) {
            if (this.touchStart) {

                this.touchStart = false;

            } else {

                if (this.isShown()) {

                    if (event.target != this.element && event.target.getParent('[data-toggle=dropdown]') != this.element) {

                        if (event.target != this.dropdown_menu && event.target.getParent('.dropdown-menu') != this.dropdown_menu) {
                            // We did not click on anything relating to this element, or this dropdown box.
                            this.hide();
                        }
                    }

                    if (event.target.getParent('[data-toggle=dropdown]') != this.element && event.target != this.element) {

                        if (typeof event.target.href != 'undefined') { // Ensure we're actually going somewhere before moving on.
                            this.hide();
                        }

                    }

                }

            }

        }.bind(this));

        element.addEvent('click', function(event) {
            this.toggle();
            event.preventDefault(); // stop a href tags on the element from going somewhere.
        }.bind(this));
    },

    toggle: function () {
        if ( this.isShown() ) {
            this.hide();
        } else {
            this.show();
        }
    },

    show: function () {
        this.touchStart = true;
        this.element.addClass('open active');
        this.dropdown_menu.show();
    },

    hide: function () {
        this.element.removeClass('open active');
        this.dropdown_menu.hide();
    },

    isShown: function () {
        if (this.dropdown_menu.isVisible() || this.element.hasClass('open')) {
            return true;
        } else {
            return false;
        }
    }
 });

/**
 * Seek out all data-toggle=modal elements, and if they have data-target="#foo" or href="#foo" - that's our target modal.
 * @return void
 */
window.addEvent('domready', function() {
    $(document.body).getElements('[data-toggle=dropdown]').each(function (element) {
        element.dropdown();
    });
});
/* =========================================================
 * bootstrap-modal.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#modals
 * =========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */

Element.implement ({
    modal: function(options) {
        if ( this.retrieve('modal') === null ) {
            this.store('modal', new Modal (options, this));
        }
        return this.retrieve('modal');
    }
});

Modal = new Class({
    Implements: [Options, Events],
    options: {
        backdrop:   true,   // Includes a modal-backdrop element. Alternatively, specify static for a backdrop which doesn't close the modal on click.
        keyboard:   true,   // Closes the modal when escape key is pressed
        show:       true,   // Shows the modal when initialized.
        remote:     false,   // If a remote url is provided, content will be loaded via jQuery's load method and injected into the .modal-body. If you're using the data api, you may alternatively use the href tag to specify the remote source. An example of this is shown below:
                            // <a data-toggle="modal" href="remote.html" data-target="#modal">click me</a>
        target:     false
    },
    initialize: function (options, selector) {
        this.browser_transition_end = this.browserTransitionEnd();

        this.selector = selector;
        this.setOptions(options);               // Merge passed options to this.options
        this.setOptions(this.getDataOptions(selector)); // Merge Data- Options to this.options

        if (this.selector.get('href')) {
            var href = this.selector.get('href');

            // If href does not start with a selector for ID or class, assume remote.
            if (href[0] != '#' || '.') {
                this.options.remote = href;
                this.selector.set('href', null);
            } else {
                this.options.target = href;
            }
        }

        if (!this.options.target) {
            return false; // No modal target? iono what to do!
        } else {
            this.element = $(document.body).getElement(this.options.target);
        }



        // Listen on Modal for data-dismiss=modal click
        this.element.addEvent('click:relay([data-dismiss=modal])', function(event) {
            this.hide(event);
        }.bind(this));

        if (this.options.remote && this.element.getElement('.modal-body')) {
            this.element.getElement('.modal-body').load(this.options.remote);
        }

        if (this.options.show) {
            this.show();
        }
    },

    toggle: function () {
        if (this.isShown) {
            this.hide();
        } else {
            this.show();
        }
    },

    show: function () {
        e = new Event.Mock(this.element, 'show');
        this.element.fireEvent('show', e);

        if (this.isShown || e.isDefaultPrevented()) {
            return;
        }

        this.isShown = true;

        this.escape();

        this.backdrop(function () {
            var transition = this.browser_transition_end && this.element.hasClass('fade');

            if (!this.element.getParent().length) {
                // don't move modals dom position
                this.element.inject($(document.body));
            }

            this.element.show();

            if (transition) {
                this.element.offsetWidth; // apparently forces reflow
            }

            this.element.addClass('in').set('aria-hidden', false);

            this.enforceFocus();

            if (transition) {
                var eventFunctionShowModal = function () {
                    clearTimeout(timeout);
                    this.element.removeEventListener(this.browser_transition_end, eventFunctionShowModal);
                    this.focusElement();
                    this.element.fireEvent('shown');
                }.bind(this);

                /* Timeout Function catches if event transition not picked up! */
                var timeout = setTimeout(function () {
                    this.element.removeEventListener(this.browser_transition_end, eventFunctionShowModal);
                    this.focusElement();
                    this.element.fireEvent('shown');
                }.bind(this), 500);

                this.element.addEventListener(this.browser_transition_end, eventFunctionShowModal);

            } else {
                this.focusElement();
                this.element.fireEvent('shown');
            }

        }.bind(this));
    },

    focusElement: function () {
        if (this.isShown) {
            this.element.setAttribute('tabIndex', 1);
            this.element.focus();
        } else {
            this.element.setAttribute('tabIndex', -1);
        }
    },

    hide: function (e) {
        e && e.preventDefault();

        e = new Event.Mock(this.element, 'hide');
        this.element.fireEvent('hide', e);

        if (!this.isShown || e.isDefaultPrevented()) {
            return;
        }

        this.isShown = false;

        this.escape();

        $(document.body).removeEvent('focus:relay(.modal)');

        this.element.removeClass('in').set('aria-hidden', true);

        if (this.browser_transition_end && this.element.hasClass('fade')) {
            this.hideWithTransition();
        } else {
            this.hideModal();
        }
    },

    enforceFocus: function () {
        $(document.body).addEvent('focus:relay(.modal)', function (e) {
            if (this.element !== e.target && !this.element.contains(e.target)) {
                this.focusElement();
            }
        }.bind(this));
    },

    escape: function () {
        if (this.isShown && this.options.keyboard) {
            this.element.addEvent('keyup', function (e) {
                if (e.key == 'esc') {
                    this.hide();
                }
            }.bind(this));
        } else if (!this.isShown) {
            this.element.removeEvent('keyup');
        }
    },

    hideWithTransition: function () {
        var eventFunctionHideModal = function () {
            clearTimeout(timeout);
            this.element.removeEventListener(this.browser_transition_end, eventFunctionHideModal);
            this.hideModal();
        }.bind(this);

        /* Timeout Function catches if event transition not picked up! */
        var timeout = setTimeout(function () {
            this.element.removeEventListener(this.browser_transition_end, eventFunctionHideModal);
            this.hideModal();
        }.bind(this), 500);

        this.element.addEventListener(this.browser_transition_end, eventFunctionHideModal);
    },

    hideModal: function () {
        this.element.hide();
        this.element.fireEvent('hidden');

        this.backdrop();
    },

    removeBackdrop: function () {
        this.backdrop_element.dispose();
        this.backdrop_element = null;
    },

    backdrop: function (callback) {
        var timeout, eventFunctionHideBackdrop;
        var animate = this.element.hasClass('fade') ? 'fade' : '';

        doAnimate = this.browser_transition_end && animate;

        if (this.isShown && this.options.backdrop) {

            this.backdrop_element = new Element('div', {'class': 'modal-backdrop ' + animate});
            $(document.body).grab(this.backdrop_element);

            if (this.options.backdrop == 'static') {

                this.backdrop_element.addEvent('click', function () {
                    this.focusElement();
                }.bind(this));

            } else {

                this.backdrop_element.addEvent('click', function () {
                    this.hide();
                }.bind(this));

            }

            if (doAnimate) {
                this.backdrop_element.offsetWidth; // apparently forces reflow
            }

            this.backdrop_element.addClass('in');

            if (doAnimate) {
                eventFunctionHideBackdrop = function () {
                    clearTimeout(timeout);
                    this.backdrop_element.removeEventListener(this.browser_transition_end, eventFunctionHideBackdrop);
                    callback();
                }.bind(this);

                /* Timeout Function catches if event transition not picked up! */
                timeout = setTimeout(function () {
                    this.backdrop_element.removeEventListener(this.browser_transition_end, eventFunctionHideBackdrop);
                    callback();
                }.bind(this), 500);

                this.backdrop_element.addEventListener(this.browser_transition_end, eventFunctionHideBackdrop);
            } else {
                callback();
            }

        } else if (callback) {

            callback();

        } else if (!this.isShown && this.backdrop_element) {

            this.backdrop_element.removeClass('in');

            if (doAnimate) {
                eventFunctionHideBackdrop = function () {
                    clearTimeout(timeout);
                    this.backdrop_element.removeEventListener(this.browser_transition_end, eventFunctionHideBackdrop);
                    this.removeBackdrop();
                }.bind(this);

                /* Timeout Function catches if event transition not picked up! */
                timeout = setTimeout(function () {
                    this.backdrop_element.removeEventListener(this.browser_transition_end, eventFunctionHideBackdrop);
                    this.removeBackdrop();
                }.bind(this), 500);

                this.backdrop_element.addEventListener(this.browser_transition_end, eventFunctionHideBackdrop);

            } else {
                this.removeBackdrop();
            }

        } else if (callback) {

            callback();

        }
    },

    /**
     * Get Options set on the Element via the dataset tags, data-animation etc.
     * @return object Key Value pair object of dataset tags.
     */
    getDataOptions: function (selector) {
        var dataset_name;
        var dataset_value;
        var options = {};
        var element = selector;

        if (typeof element.dataset != 'undefined') {

            for (dataset_name in element.dataset) {

                dataset_value = this.trueValue( element.dataset[dataset_name] );

                options[dataset_name] = dataset_value;
            }

            return options;

        } else if (Browser.ie) {

            // Cycle through options name to find data-<name> values where dataset is not available to us.
            for (dataset_name in this.options) {

                if (element.get('data-' + dataset_name)) {

                    options[dataset_name] = this.trueValue( element.get('data-' + dataset_name) );

                }

            }

            return options;

        } else {

            // Can't find data options, return empty object
            return {};

        }
    },

    /**
     * trueValue convert strings to their literals.
     * @param  string value String Value to convert to literal
     * @return mixed        Literal value of string where applicable, or the string.
     */
    trueValue: function (value) {
        if (value == 'true') {
            return true;
        } else if (value == 'false') {
            return false;
        } else if (value == 'null') {
            return null;
        } else {
            return value;
        }
    },

    /**
     * Find the supported browser transition end event name.
     * @return mixed false on unsupported, or string of the event name
     */
    browserTransitionEnd: function (){
        var t;
        var el = document.createElement('fakeelement');
        var transitions = {
          'transition':'transitionEnd',
          'OTransition':'oTransitionEnd',
          'MSTransition':'msTransitionEnd',
          'MozTransition':'transitionend',
          'WebkitTransition':'webkitTransitionEnd'
        };

        for(t in transitions){
            if( el.style[t] !== undefined ){
                return transitions[t];
            }
        }

        return false;
    }

});

/**
 * http://davidwalsh.name/mootools-event
 * creates a Mock event to be used with fire event
 * @param Element target an element to set as the target of the event - not required
 *  @param string type the type of the event to be fired. Will not be used by IE - not required.
 *
 */
Event.Mock = function(target,type){
    var e = window.event;
    type = type || 'click';

    if (document.createEvent){
        e = document.createEvent('HTMLEvents');
        e.initEvent(
          type, //event type
          false, //bubbles - set to false because the event should like normal fireEvent
          true //cancelable
        );
    }
    e = new Event(e);
    e.target = target;
    return e;
};

Event.implement({
    isDefaultPrevented: function () {
        return this.event.defaultPrevented;
    }
});

/**
 * Seek out all data-toggle=modal elements, and if they have data-target="#foo" or href="#foo" - that's our target modal.
 * @return void
 */
window.addEvent('domready', function() {
    $(document.body).getElements('[data-toggle=modal]').each(function (element) {
        if (element.get('data-target') !== null || element.get('href') !== null) {
            // Don't automagically listen unless we have data-target or a href
            element.addEvent('click', function() {
                element.modal({show: false}).toggle();
            });
        }
    });
});
/* ===========================================================
 * bootstrap-tooltip.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#tooltips
 * Inspired by the original jQuery.tipsy by Jason Frame
 * ===========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

Element.implement ({
    tooltip: function(options) {
        if ( this.retrieve('tooltip') === null ) {
            this.store('tooltip', new Tooltip (options, this));
        }
        return this.retrieve('tooltip');
    }
});

Tooltip = new Class({
    Implements: [Options],
    options: {
        animation: true,        // apply a css fade transition to the tooltip

        html: false,            // Insert html into the tooltip. If false, jquery's text method will be used to insert content into the dom. Use text if you're worried about XSS attacks.

        placement: 'top',       // string|function - top | bottom | left | right

        selector: false,        // If a selector is provided, tooltip objects will be delegated to the specified targets.

        title: '',              // default title value if `title` tag isn't present

        trigger: 'hover',       // how tooltip is triggered - click | hover | focus | manual

        delay: 0,               // delay showing and hiding the tooltip (ms) - does not apply to manual trigger type
                                // If a number is supplied, delay is applied to both hide/show
                                // Object structure is: delay: { show: 500, hide: 100 }

        document_placement: false,  // MOOBOO SPECIFIC OPTION
                                    //  false:  Tip inserts itself after selector/element
                                    //  true:   Tip inserts itself into the end of the $(document.body)
                                    //  string: ID Selector for Tip Placement, to be placed inside
                                    //  {Mootools Element}: Tip inserts inside given element
                                    //
                                    //  By default, bootstrap Tips will place themselves just after the selector/element - which can cause stupid css inheritance issues.

        template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'

        // Heads up! Options for individual tooltips can alternatively be specified through the use of data attributes.
    },

    initialize: function (options, element) {
        var eventIn, eventOut;

        this.enabled    = true;
        this.tip        = false;
        this.hoverState = false;
        this.element    = element;

        this.setOptions(options);               // Merge passed options to this.options
        this.setOptions(this.getDataOptions()); // Merge Data- Options to this.options


        if (!this.options.selector) {

            // Default Tooltip appearance to this element.
            this.options.selector = this.element;

        } else if (!isElement(this.options.selector)) {

            // If options selector isn't already a HTML element, make it so.
            this.options.selector = $(this.options.selector);

        }


        if ( this.options.trigger == 'click' ) {

            this.element.addEvent('click', this.toggle.bind(this));

        } else if ( this.options.trigger != 'manual' ) {

            eventIn  = (this.options.trigger == 'hover' ? 'mouseenter' : 'focus');
            eventOut = (this.options.trigger == 'hover' ? 'mouseleave' : 'blur');

            this.element.addEvent(eventIn, this.enter.bind(this));
            this.element.addEvent(eventOut, this.leave.bind(this));
        }

        this.fixTitle();

        if (this.options.delay && typeof this.options.delay == 'number') {

            this.options.delay = {
                show: this.options.delay,
                hide: this.options.delay
            };

        }

    },

    enter: function () {
        var delay = this.options.delay.show;

        if (!delay) return this.show();

        clearTimeout(this.timeout);

        this.hoverState = 'in';

        this.timeout = setTimeout(function() {
            if (this.hoverState == 'in') {
                this.show();
            }
        }.bind(this), delay);
    },

    leave: function () {
        var delay = this.options.delay.hide;

        if (!delay) return this.hide();

        clearTimeout(this.timeout);

        this.hoverState = 'out';

        this.timeout = setTimeout(function() {
            if (this.hoverState == 'out') {
                this.hide();
            }
        }.bind(this), delay);
    },

    fixTitle: function () {
        var title = this.element.get('title');

        if (!this.options.title) {
            if (title) {
                this.options.title = title;
                this.element.set('data-original-title', title);
                this.element.set('title', null);
            }
        }

    },

    getTitle: function () {
        return this.options.title;
    },

    hasContent: function () {
        return this.getTitle();
    },

    setContent: function () {
        var tip           = this.getTip();
        var title         = this.getTitle();
        var insert_method = this.options.html ? 'html' : 'text';

        tip.getElement('.tooltip-inner').set(insert_method, title);

        tip.removeClass('fade in top bottom left right');

        return this;
    },

    /**
     * Show Tooltip
     * @return void
     */
    show: function () {
        var tip;
        var placement = this.options.placement;
        var selector  = this.options.selector;

        if (this.hasContent() && this.enabled) {
            tip = this.getTip();
            this.setContent();

            if (this.options.animation) {

                tip.addClass('fade');

            }

            tip = tip.dispose();
            tip.setStyle('display', 'block');

            if (this.options.document_placement) {
                if (this.options.document_placement === true) {
                    $(document.body).grab(tip);
                } else if (isElement(this.options.document_placement)) {
                    this.options.document_placement.grab(tip);
                } else {
                    $(this.options.document_placement).grab(tip);
                }
            } else {
                tip.inject(selector, 'after');
            }

            if (typeof placement == 'function') {

                // I'm expecting the function to take the tip and selector and return me back the tip
                tip = placement(tip, selector);

            } else {

                tip.addClass(placement);

                // Calculate Sizes for Selector and Tip
                var tip_size      = tip.getComputedSize();
                var offset_amount;

                /**
                 * Mootools edge positions were inconsistant in this situation,
                 * calculating offset from relativeToElement manually.
                 */
                switch (placement) {
                    case 'top':
                        offset_amount = {
                            x: -(tip_size.totalWidth / 2),
                            y: -(tip_size.totalHeight)
                        };

                        tip.position({
                            'relativeTo': selector,
                            'position': 'centerTop',
                            'offset': offset_amount
                        });
                        break;

                    case 'right':
                        offset_amount = {
                            x: 0,
                            y: -(tip_size.totalHeight / 2)
                        };
                        tip.position({
                            'relativeTo': selector,
                            'position': 'centerRight',
                            'offset': offset_amount
                        });
                        break;

                    case 'bottom':
                        offset_amount = {
                            x: -(tip_size.totalWidth / 2),
                            y: 0
                        };
                        tip.position({
                            'relativeTo': selector,
                            'position': 'centerBottom',
                            'offset': offset_amount
                        });
                        break;

                    case 'left':
                        offset_amount = {
                            x: -(tip_size.totalWidth),
                            y: -(tip_size.totalHeight / 2)
                        };
                        tip.position({
                            'relativeTo': selector,
                            'position': 'centerLeft',
                            'offset': offset_amount
                        });
                        break;
                }

            }

            tip.addClass('in');

        }
        return this;
    },

    getTip: function () {

        if (typeof this.tip == 'undefined' || this.tip === false) {

            this.tip = new Elements.from(this.options.template);

        }

        return this.tip[0];
    },

    /**
     * Hide Tooltip
     * @return void
     */
    hide: function () {
        var tip       = this.getTip();
        var animation = this.options.animation;

        tip.removeClass('in');

        if (animation) {
            var timeout = setTimeout(function () {
                tip.dispose();
                clearTimeout(timeout);
            }, 500);
        } else {
            tip.dispose();
        }

        return this;
    },

    /**
     * Toggle Tooltip Visibility
     * @return void
     */
    toggle: function () {
        if (this.hoverState == 'in') {
            this.hoverState = 'out';
            this.hide();
        } else {
            this.hoverState = 'in';
            this.show();
        }
    },

    isShown: function () {
        return this.isShown;
    },

    disable: function () {
        this.enabled = false;
        return this;
    },

    enable: function () {
        this.enabled = true;
        return this;
    },

    toggleEnabled: function () {
        this.enabled = !this.enabled;
        return this;
    },

    /**
     * Destroy instance of Tooltip
     * @return void
     */
    destroy: function () {
        this.enabled = false;
        this.tip.dispose();
        this.element.store('tooltip', null);
    },

    /**
     * Get Options set on the Element via the dataset tags, data-animation etc.
     * @return object Key Value pair object of dataset tags.
     */
    getDataOptions: function () {
        var dataset_name;
        var dataset_value;
        var options = {};
        var element = this.element;

        if (typeof element.dataset != 'undefined') {

            for (dataset_name in element.dataset) {

                dataset_value = this.trueValue( element.dataset[dataset_name] );

                options[dataset_name] = dataset_value;
            }

            return options;

        } else if (Browser.ie) {

            // Cycle through options name to find data-<name> values where dataset is not available to us.
            for (dataset_name in this.options) {

                if (element.get('data-' + dataset_name)) {

                    options[dataset_name] = this.trueValue( element.get('data-' + dataset_name) );

                }

            }

            return options;

        } else {

            // Can't find data options, return empty object
            return {};

        }
    },

    /**
     * trueValue convert strings to their literals.
     * @param  string value String Value to convert to literal
     * @return mixed        Literal value of string where applicable, or the string.
     */
    trueValue: function (value) {
        if (value == 'true') {
            return true;
        } else if (value == 'false') {
            return false;
        } else if (value == 'null') {
            return null;
        } else {
            return value;
        }
    }
});

/**
 * Full Credit here.
 * http://stackoverflow.com/questions/384286/javascript-isdom-how-do-you-check-if-a-javascript-object-is-a-dom-object
 */
function isElement(obj) {
    try {
        //Using W3 DOM2 (works for FF, Opera and Chrom)
        return obj instanceof HTMLElement;
    }
    catch(e){
        //Browsers not supporting W3 DOM2 don't have HTMLElement and
        //an exception is thrown and we end up here. Testing some
        //properties that all elements have. (works on IE7)
    return (typeof obj==="object") &&
      (obj.nodeType===1) && (typeof obj.style === "object") &&
      (typeof obj.ownerDocument ==="object");
    }
}
/* ===========================================================
 * bootstrap-tooltip.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#tooltips
 * Inspired by the original jQuery.tipsy by Jason Frame
 * ===========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

Element.implement ({
    popover: function(options) {
        if ( this.retrieve('popover') === null ) {
            this.store('popover', new Popover (options, this));
        }
        return this.retrieve('popover');
    }
});

Popover = new Class({
    Extends: Tooltip,
    options: {
        animation:  true,       // apply a css fade transition to the tooltip
        html:       false,      // Insert html into the popover. If false, jquery's text method will be used to insert content into the dom. Use text if you're worried about XSS attacks.
        placement:  'right',    // how to position the popover - top | bottom | left | right
        selector:   false,      // if a selector is provided, tooltip objects will be delegated to the specified targets
        trigger:    'click',    // how popover is triggered - click | hover | focus | manual
        title:      '',         // default title value if `title` attribute isn't present
        content:    '',         // default content value if `data-content` attribute isn't present
        delay:      0,          // delay showing and hiding the popover (ms) - does not apply to manual trigger type
                                // If a number is supplied, delay is applied to both hide/show
                                //  Object structure is: delay: { show: 500, hide: 100 }
        template: '<div class="popover"><div class="arrow"></div><div class="popover-inner"><h3 class="popover-title"></h3><div class="popover-content"></div></div></div>'
    },
    initialize: function (options, element) {
        this.parent(options, element);
    },

    setContent: function () {
        var tip           = this.getTip();
        var title         = this.getTitle();
        var content       = this.getContent();
        var insert_method = this.options.html ? 'html' : 'text';

        tip.getElement('.popover-title').set(insert_method, title);
        tip.getElement('.popover-content').set(insert_method, content);

        tip.removeClass('fade top bottom left right in');
    },

    hasContent: function () {
        return this.getTitle() || this.getContent();
    },

    getContent: function () {
        return this.options.content;
    }

});
/* ========================================================
 * bootstrap-tab.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#tabs
 * ========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================== */

/**
 * @todo  Support Dropdown menu items in the list of tabs
 */

Element.implement ({
    tab: function(options) {
        if ( this.retrieve('tab') === null ) {
            this.store('tab', new Tab (options, this));
        }
        return this.retrieve('tab');
    }
});

Tab = new Class({
    Implements: [Options, Events],
    options: {

    },

    initialize: function (options, tab) {
        this.tab  = tab;
        this.list = tab.getParent('ul:not(.dropdown-menu)');

        if (!this.list) {
            return; // Unable to find parent ul to sit on.
        }

        this.setOptions(options);
        this.setOptions(this.getDataOptions(this.tab));

        if (!this.options.target) {
            this.selector = this.tab.get('href');
            this.selector = this.selector && this.selector.replace(/.*(?=#[^\s]*$)/, ''); //strip for ie7
            this.selector = $(document.body).getElement(this.selector);
        }

        this.tab.addEvent('click', function(event) {
            event.preventDefault();
            this.show();
        }.bind(this));
    },

    show: function () {
        if (this.tab.getParent('li').hasClass('active')) return;

        previous = this.list.getElement('.active:last a');

        e = new Event.Mock(this.tab, { type: 'show', relatedTarget: previous });
        this.tab.fireEvent('show', e);

        if (e.isDefaultPrevented()) return; // Someone stopped the event;

        this.activate(this.tab.getParent('li'), this.list);
        this.activate(this.selector, this.selector.getParent(), function () {
            this.tab.fireEvent('shown', new Event.Mock(this.tab, { type: 'shown', relatedTarget: previous }));
        }.bind(this));
    },

    activate: function (element, container, callback) {
        var active = container.getElement('> .active');
        var transition = callback && this.browserTransitionEnd() && active.hasClass('fade');

        function next() {
            active.removeClass('active');

            if (active.getElement('> .dropdown-menu > .active')) {
                active.getElement('> .dropdown-menu > .active').removeClass('active');
            }

            element.addClass('active');

            if (transition) {
                element[0].offsetWidth // reflow for transition
                element.addClass('in');
            } else {
                element.removeClass('fade');
            }

            if (element.getParent('.dropdown-menu')) {
                element.getElement('li.dropdown').addClass('active');
            }

            callback && callback();
        }

        transition ? active.addEventListener(this.browser_transition_end, next) : next();

        active.removeClass('in');

    },

    /**
     * Get Options set on the Element via the dataset tags, data-animation etc.
     * @return object Key Value pair object of dataset tags.
     */
    getDataOptions: function (selector) {
        var dataset_name;
        var dataset_value;
        var options = {};
        var element = selector;

        if (typeof element.dataset != 'undefined') {

            for (dataset_name in element.dataset) {

                dataset_value = this.trueValue( element.dataset[dataset_name] );

                options[dataset_name] = dataset_value;
            }

            return options;

        } else if (Browser.ie) {

            // Cycle through options name to find data-<name> values where dataset is not available to us.
            for (dataset_name in this.options) {

                if (element.get('data-' + dataset_name)) {

                    options[dataset_name] = this.trueValue( element.get('data-' + dataset_name) );

                }

            }

            return options;

        } else {

            // Can't find data options, return empty object
            return {};

        }
    },

    /**
     * trueValue convert strings to their literals.
     * @param  string value String Value to convert to literal
     * @return mixed        Literal value of string where applicable, or the string.
     */
    trueValue: function (value) {
        if (value == 'true') {
            return true;
        } else if (value == 'false') {
            return false;
        } else if (value == 'null') {
            return null;
        } else {
            return value;
        }
    },

    browserTransitionEnd: function () {
        var t;
        var el = document.createElement('fakeelement');
        var transitions = {
          'transition':'transitionEnd',
          'OTransition':'oTransitionEnd',
          'MSTransition':'msTransitionEnd',
          'MozTransition':'transitionend',
          'WebkitTransition':'webkitTransitionEnd'
        };

        for(t in transitions){
            if( el.style[t] !== undefined ){
                return transitions[t];
            }
        }

        return false;
    }
});

/**
 * http://davidwalsh.name/mootools-event
 * creates a Mock event to be used with fire event
 * @param Element target an element to set as the target of the event - not required
 *  @param string type the type of the event to be fired. Will not be used by IE - not required.
 *
 */
Event.Mock = function(target,type){
    var e = window.event;
    type = type || 'click';

    if (document.createEvent){
        e = document.createEvent('HTMLEvents');
        e.initEvent(
          type, //event type
          false, //bubbles - set to false because the event should like normal fireEvent
          true //cancelable
        );
    }
    e = new Event(e);
    e.target = target;
    return e;
};

Event.implement({
    isDefaultPrevented: function () {
        return this.event.defaultPrevented;
    }
});

/**
 * Seek out all data-toggle=tab elements, and we have target tabs.
 * @return void
 */
window.addEvent('domready', function() {
    $(document.body).getElements('[data-toggle=tab]').each(function (element) {
        if (element.get('data-target') !== null || element.get('href') !== null) {
            element.tab();
        }
    });
});
 /**
  * Inspired from https://github.com/nostalgiaz/bootstrap-toggle-buttons
  * Copyright: Sentral Education 2013 by Darren Nolan.
  * etc. etc.
  *
  * Takes a checkbox, and makes it purdy bootstrap buttons and toggles states.
  * @todo : Animation of the toggle.
  *
  *
  * Options:
  *  size: (as per bootstrap buttons) large, small, mini or false for default)
  *  enabled: (as per bootstrap buttons) primary, danger, info, success, warning or false for default button color.
  *  enabledtext: Text Label for Enabled/Active button
  *  disabled: (as per bootstrap buttons) primary, danger, info, success, warning or false for default button color.
  *  disabledtext: Text Label for Enabled/Active button
  *
  *  animation: boolean = CSS3 transition between button states.  Fixed at 0.2s at this stage.
  *
  *
  * Usage:
  *
  * <div data-toggle="toggleButtons" data-enabledtext="Shown" data-disabledtext="Hidden">
  *   <input type="checkbox" id="showWeekends">
  * </div>
  *
  *
  */
 Element.implement ({
    toggleButtons: function (options) {
        if (this.retrieve('toggleButtons') === null) {
            this.store('toggleButtons', new toggleButtons (options, this));
        }
        return this.retrieve('toggleButtons');
    }
 });

 toggleButtons = new Class({
    Implements: [Options, Events],
    options: {
        size:        false,    // 'large', 'small', 'mini' or false for default button size.

        enabled:     'primary',  // 'primary', 'danger', 'info', 'success', 'warning' or false for default button color.
        enabledtext: 'ON',

        disabled:    false,
        disabledtext:'OFF',

        animation:    true
    },

    initialize: function (options, element) {

        this.element  = element;
        this.checkbox = element.getElement('input[type=checkbox]');

        this.setOptions(options);               // Merge passed options to this.options
        this.setOptions(this.getDataOptions()); // Merge Data- Options to this.options

        this.enabled = true;

        if (this.checkbox.checked) {
            this.state = true;
        } else {
            this.state = false;
        }

        this.displayButtons();

        this.checkbox.addEvent('change', function() {
            if (this.checkbox.checked) {
                this.setOn(true);
            } else {
                this.setOff(true);
            }
        }.bind(this));
    },

    displayButtons: function () {
        this.checkbox.hide();
        this.element.addClass('toggle-buttons');

        this.button_container = new Element('div').addClass('btn-group');

        this.button_blank = new Element('button').addClass('btn');
        if (this.options.size) {
            this.button_blank.addClass('btn-' + this.options.size);
        }


        this.button_left = this.button_blank.clone().set('text', this.options.enabledtext);
        if (this.options.enabled) {
            this.button_left.addClass('btn-' + this.options.enabled);
        }
        this.button_right = this.button_blank.clone().set('text', this.options.disabledtext);
        if (this.options.disabled) {
            this.button_right.addClass('btn-' + this.options.disabled);
        }


        this.button_container
            .grab(this.button_left)
            .grab(this.button_blank)
            .grab(this.button_right);

        this.button_container.inject(this.element, 'bottom');

        this.button_container.disableSelection();

        var button_left_size  = this.button_left.measure( function() { return this.getSize(); });
        var button_right_size = this.button_right.measure( function() { return this.getSize(); });
        var button_width      = (button_left_size.x > button_right_size.x) ? button_left_size.x : button_right_size.x;
        var button_height     = (button_left_size.y > button_right_size.y) ? button_left_size.y : button_right_size.y;

        var button_styles = {
            'position': 'absolute',
            'width':    button_width -1,
            'height':   button_height,
            'margin-left': 0
        };

        if (this.checked()) {
            this.button_left.setStyles(button_styles).setStyle('left', '0%');
            this.button_blank.setStyles(button_styles).setStyle('left', '50%');
            this.button_right.setStyles(button_styles).setStyle('left', '100%');

            this.button_blank.style.borderTopRightRadius    = '4px';
            this.button_blank.style.borderBottomRightRadius = '4px';
            this.button_blank.style.borderTopLeftRadius     = '0px';
            this.button_blank.style.borderBottomLeftRadius  = '0px';
        } else {
            this.button_left.setStyles(button_styles).setStyle('left', '-50%');
            this.button_blank.setStyles(button_styles).setStyle('left', '0%');
            this.button_right.setStyles(button_styles).setStyle('left', '50%');

            this.button_blank.style.borderTopRightRadius    = '0px';
            this.button_blank.style.borderBottomRightRadius = '0px';
            this.button_blank.style.borderTopLeftRadius     = '4px';
            this.button_blank.style.borderBottomLeftRadius  = '4px';
        }

        if (this.options.animation) {
            this.button_left.style.transition = 'left 0.2s';
            this.button_blank.style.transition = 'left 0.2s';
            this.button_right.style.transition = 'left 0.2s';

            this.button_left.style.WebkitTransition = 'left 0.2s';
            this.button_blank.style.WebkitTransition = 'left 0.2s';
            this.button_right.style.WebkitTransition = 'left 0.2s';

            this.button_left.style.MozTransition = 'left 0.2s';
            this.button_blank.style.MozTransition = 'left 0.2s';
            this.button_right.style.MozTransition = 'left 0.2s';
        }

        this.button_container.setStyles({
            'overflow': 'hidden',
            'width':    (button_width * 2) -2,
            'height':   button_height
        });

        this.button_container.style.borderRadius    = '4px';
        this.button_container.style.MozBorderRadius = '4px';

        this.button_left.addEvent('click', function (event) {
            event.stop();
            this.toggleState();
        }.bind(this));
        this.button_right.addEvent('click', function (event) {
            event.stop();
            this.toggleState();
        }.bind(this));
        this.button_blank.addEvent('click', function (event) {
            event.stop();
            this.toggleState();
        }.bind(this));

    },

    deleteButtons: function () {
        this.enabled = false;
        this.checkbox.show();
        this.element.removeClass('btn-group');
        this.button_container.dispose();
    },

    toggleState: function () {
        if (this.state) {
            this.setOff();
        } else {
            this.setOn();
        }
    },

    setOn: function (triggered_manually) {
        if (typeof triggered_manually == 'undefined' || triggered_manually === false) {

            e = new Event.Mock(this.element, 'setoff');
            this.element.fireEvent('seton', e);

            if (!this.enabled || e.isDefaultPrevented()) {
                return;
            }

            e = new Event.Mock(this.checkbox, 'change');
            this.checkbox.fireEvent('change', e);

            this.checkbox.checked = true;

        }

        this.state = true;

        this.button_left.setStyle('left', '0%');
        this.button_blank.setStyle('left', '50%');
        this.button_right.setStyle('left', '100%');

        this.button_blank.style.borderTopRightRadius    = '4px';
        this.button_blank.style.borderBottomRightRadius = '4px';
        this.button_blank.style.borderTopLeftRadius     = '0px';
        this.button_blank.style.borderBottomLeftRadius  = '0px';

    },

    setOff: function (triggered_manually) {
        if (typeof triggered_manually == 'undefined' || triggered_manually === false) {

            e = new Event.Mock(this.element, 'setoff');
            this.element.fireEvent('setoff', e);

            if (!this.enabled || e.isDefaultPrevented()) {
                return;
            }

            e = new Event.Mock(this.checkbox, 'change');
            this.checkbox.fireEvent('change', e);

            this.checkbox.checked = false;

        }

        this.state = false;

        this.button_left.setStyle('left', '-50%');
        this.button_blank.setStyle('left', '0%');
        this.button_right.setStyle('left', '50%');

        this.button_blank.style.borderTopRightRadius    = '0px';
        this.button_blank.style.borderBottomRightRadius = '0px';
        this.button_blank.style.borderTopLeftRadius     = '4px';
        this.button_blank.style.borderBottomLeftRadius  = '4px';

    },

    checked: function () {
        return this.state;
    },

    enable: function () {
        this.enabled = true;
        this.button_left.removeClass('disabled');
        this.button_right.removeClass('disabled');
        return this;
    },
    disable: function () {
        this.enabled = false;
        this.button_left.addClass('disabled');
        this.button_right.addClass('disabled');
        return this;
    },

    /**
     * Get Options set on the Element via the dataset tags, data-animation etc.
     * @return object Key Value pair object of dataset tags.
     */
    getDataOptions: function () {
        var dataset_name;
        var dataset_value;
        var options = {};
        var element = this.element;

        if (typeof element.dataset != 'undefined') {

            for (dataset_name in element.dataset) {

                dataset_value = this.trueValue( element.dataset[dataset_name] );

                options[dataset_name] = dataset_value;
            }

            return options;

        } else if (Browser.ie) {

            // Cycle through options name to find data-<name> values where dataset is not available to us.
            for (dataset_name in this.options) {

                if (element.get('data-' + dataset_name)) {

                    options[dataset_name] = this.trueValue( element.get('data-' + dataset_name) );

                }

            }

            return options;

        } else {

            // Can't find data options, return empty object
            return {};

        }
    },

    /**
     * trueValue convert strings to their literals.
     * @param  string value String Value to convert to literal
     * @return mixed        Literal value of string where applicable, or the string.
     */
    trueValue: function (value) {
        if (value == 'true') {
            return true;
        } else if (value == 'false') {
            return false;
        } else if (value == 'null') {
            return null;
        } else {
            return value;
        }
    }


});

 /**
 * http://davidwalsh.name/mootools-event
 * creates a Mock event to be used with fire event
 * @param Element target an element to set as the target of the event - not required
 *  @param string type the type of the event to be fired. Will not be used by IE - not required.
 *
 */
Event.Mock = function(target,type){
    var e = window.event;
    type = type || 'click';

    if (document.createEvent){
        e = document.createEvent('HTMLEvents');
        e.initEvent(
          type, //event type
          false, //bubbles - set to false because the event should like normal fireEvent
          true //cancelable
        );
    }
    e = new Event(e);
    e.target = target;
    return e;
};

Event.implement({
    isDefaultPrevented: function () {
        return this.event.defaultPrevented;
    }
});

Element.implement({
    disableSelection: function(){
        if (typeof this.onselectstart!="undefined") //IE route
            this.onselectstart=function(){return false;};
        else if (typeof this.style.MozUserSelect!="undefined") //Firefox route
            this.style.MozUserSelect="none";
        else //All other route (ie: Opera)
            this.onmousedown=function(){return false;};
        this.style.cursor = "default";
        return this;
        //this.addClass('-webkit-user-select','none');
    }
});

/**
 * Seek out all data-toggle=modal elements, and if they have data-target="#foo" or href="#foo" - that's our target modal.
 * @return void
 */
window.addEvent('domready', function() {
    $(document.body).getElements('[data-toggle=toggleButtons]').each(function (element) {
        element.toggleButtons();
    });
});


/* ===================================================
 * bootstrap-transition.js v2.0.4
 * http://twitter.github.com/bootstrap/javascript.html#transitions
 * ===================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Modified for MooTools by GP Technology Solutions Pty Ltd
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

var Transition = function () {

    var transitionEnd = (function () {

        var el = document.createElement('bootstrap');
        var transEndEventNames = {
            'WebkitTransition' : 'webkitTransitionEnd',
            'MozTransition'    : 'transitionend',
            'OTransition'      : 'oTransitionEnd',
            'msTransition'     : 'MSTransitionEnd',
            'transition'       : 'transitionend'
        }
        var name;

        for (name in transEndEventNames){
            if (el.style[name] !== undefined) {
                return transEndEventNames[name]
            }
        }

    }());

    return transitionEnd && {
        end: transitionEnd
    }

}
/* =============================================================
 * bootstrap-typeahead.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#typeahead
 * =============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */

 Element.implement ({
    typeahead: function(options) {
        if ( this.retrieve('Typeahead') === null ) {
            this.store('Typeahead', new Typeahead (options, this));
        }
        return this.retrieve('Typeahead');
    }
});

Typeahead = new Class({
    Implements: [Options, Events],
    options: {
        source:      [],    // The data source to query against. May be an array of strings or a function.
                            // The function is passed two arguments, the query value in the input field
                            // and the process callback. The function may be used synchronously by returning
                            // the data source directly or asynchronously via the process callback's single argument.

        items:       8,     // The max number of items to display in the dropdown.

        minLength:   1,     // The minimum character length needed before triggering autocomplete suggestions

        matcher:     {},    // The method used to determine if a query matches an item. Accepts a single argument, the item
                            // against which to test the query. Access the current query with this.query. Return a boolean true if query is a match.

        sorter:      {},    // Method used to sort autocomplete results. Accepts a single argument items and has the scope of the
                            // typeahead instance. Reference the current query with this.query.

        updater:     {},    // The method used to return selected item. Accepts a single argument, the item and has the scope of the typeahead instance.

        highlighter: {},    // Method used to highlight autocomplete results. Accepts a single argument item and has the scope of the typeahead instance. Should return html.

        menu:        '<ul class="typeahead dropdown-menu"></ul>',
        item:        '<li><a href="#"></a></li>'
    },

    initialize: function (options, element) {
        this.element = element;
        this.setOptions(options);

        this.menu = new Elements.from(this.options.menu)[0];
        this.item = new Elements.from(this.options.item)[0];

        this.listen();
    },

    select: function () {
        var active = this.menu.getElement('.active');
        var val    = active.get('data-value');

        this.element
            .set('value', this.updater(val))
            .fireEvent('change', new Event.Mock(this.element, 'change'));

        return this.hide();
    },

    updater: function (item) {
        return item;
    },

    show: function () {
        var pos = Object.merge({}, this.element.getCoordinates(), {
            height: this.element.offsetHeight
        });

        this.menu.inject(this.element, 'after')
            .setStyles({
                top: pos.top + pos.height,
                left: pos.left
            })
            .show();

        this.shown = true;

        return this;
    },

    hide: function () {
        this.menu.hide();
        this.shown = false;
        return this;
    },

    lookup: function (event) {
        var items;

        this.query = this.element.get('value');

        if (!this.query || this.query.length < this.options.minLength) {
            return this.shown ? this.hide() : this;
        }

        items = typeof this.options.source == 'function' ? this.options.source(this.query, this.process.bind(this)) : this.options.source;

        return items ? this.process(items) : this;
    },

    process: function (items) {
        items = items.filter(function (item) {
            return this.matcher(item);
        }.bind(this));

        items = this.sorter(items);

        if (!items.length) {
            return this.shown ? this.hide() : this;
        }

        return this.render(items.slice(0, this.options.items)).show();
    },

    matcher: function (item) {
        return ~item.toLowerCase().indexOf(this.query.toLowerCase());
    },

    sorter: function (items) {
        var beginswith = [];
        var caseSensitive = [];
        var caseInsensitive = [];
        var item;

        while (item = items.shift()) {
            if (!item.toLowerCase().indexOf(this.query.toLowerCase())) {
                beginswith.push(item);
            } else if (~item.indexOf(this.query)) {
                caseSensitive.push(item);
            } else  {
                caseInsensitive.push(item);
            }
        }

        return beginswith.concat(caseSensitive, caseInsensitive);
    },

    highlighter: function (item) {
        var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&');
        return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
            return '<strong>' + match + '</strong>';
        });
    },

    render: function (items) {
        items = items.map(function (item, i) {
            i = this.item.clone();
            i.set('data-value', item);
            i.getElement('a').set('html', this.highlighter(item));
            return i;
        }.bind(this));

        this.menu.set('html', null);

        items.each(function (item) {
            item.inject(this.menu);
        }.bind(this));

        this.menu.getFirst('li').addClass('active');
        return this;
    },

    next: function (event) {
        var active = this.menu.getElement('.active').removeClass('active');
        var next = active.getNext();

        if (next === null) {
            next = this.menu.getElement('li');
        }

        next.addClass('active');
    },

    prev: function (event) {
        var active = this.menu.getElement('.active').removeClass('active');
        var prev   = active.getPrevious();

        if (prev === null) {
            prev = this.menu.getLast('li');
        }

        prev.addClass('active');
    },

    listen: function () {
        this.element.addEvents({
            'blur': function (e) {
                this.blur(e);
            }.bind(this),
            'keypress': function (e) {
                this.keypress(e);
            }.bind(this),
            'keyup': function (e) {
                this.keyup(e);
            }.bind(this)
        });

        if (this.eventSupported('keydown')) {
            this.element.addEvent('keydown', function (e) {
                this.keydown(e);
            }.bind(this));
        }

        this.menu.addEvents({
            'click:relay(li)': function (e) {
                this.click(e);
            }.bind(this),
            'mouseenter:relay(li)': function (e) {
                this.mouseenter(e);
            }.bind(this)
        });
    },

    eventSupported: function (eventName) {
        var isSupported = typeof this.element['on' + eventName] != 'undefined';

        if (!isSupported) {
            this.element.set('on' + eventName, 'return;');
            isSupported = typeof this.element['on' + eventName] === 'function';
        }

        return isSupported;
    },

    move: function (e) {
        if (!this.shown) return;

        switch(e.code) {
            case 9: // tab
            case 13: // enter
            case 27: // escape
                e.preventDefault();
                break;

            case 38: // up arrow
                e.preventDefault();
                this.prev();
                break;

            case 40: // down arrow
                e.preventDefault();
                this.next();
                break;
        }

        e.stopPropagation();
    },

    keydown: function (e) {
        this.suppressKeyPressRepeat = ~[40,38,9,13,27].indexOf(e.code);
        this.move(e);
    },

    keypress: function (e) {
        if (this.suppressKeyPressRepeat) return;
        this.move(e);
    },

    keyup: function (e) {
        switch(e.code) {
            case 40: // down arrow
            case 38: // up arrow
            case 16: // shift
            case 17: // ctrl
            case 18: // alt
                break;

            case 9: // tab
            case 13: // enter
                if (!this.shown) return;
                this.select();
                break;

            case 27: // escape
                if (!this.shown) return;
                this.hide();
                break;

            default:
                this.lookup();
      }

      e.stopPropagation();
      e.preventDefault();
    },

    blur: function (e) {
        setTimeout(function () {
            this.hide();
        }.bind(this), 150);
    },

    click: function (e) {
        e.stopPropagation();
        e.preventDefault();
        this.select();
    },

    mouseenter: function (e) {
        var target = e.target.get('data-value') === null ? e.target.getParent('li') : e.target;
        this.menu.getElements('.active').removeClass('active');
        target.addClass('active');
    }
});

/**
 * http://davidwalsh.name/mootools-event
 * creates a Mock event to be used with fire event
 * @param Element target an element to set as the target of the event - not required
 *  @param string type the type of the event to be fired. Will not be used by IE - not required.
 *
 */
Event.Mock = function(target,type){
    var e = window.event;
    type = type || 'click';

    if (document.createEvent){
        e = document.createEvent('HTMLEvents');
        e.initEvent(
          type, //event type
          false, //bubbles - set to false because the event should like normal fireEvent
          true //cancelable
        );
    }
    e = new Event(e);
    e.target = target;
    return e;
};

Event.implement({
    isDefaultPrevented: function () {
        return this.event.defaultPrevented;
    }
});


/**
 * Seek out all data-provide=typeahead elements, bind on focus.
 * @return void
 */

window.addEvent('domready', function() {
    $(document.body).getElements('[data-provide=typeahead]').each(function (element) {
        element.typeahead();
    });
});
