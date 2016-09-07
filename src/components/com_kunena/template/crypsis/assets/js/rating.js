(function () {

	'use strict';

	/**
	 * rating
	 *
	 * @description The rating component.
	 * @param {HTMLElement} el The HTMl element to build the rating widget on
	 * @param {Number} currentRating The current rating value
	 * @param {Number} maxRating The max rating for the widget
	 * @param {Function} callback The optional callback to run after set rating
	 * @return {Object} Some public methods
	 */
	function rating(el, currentRating, maxRating, callback) {

		/**
		 * stars
		 *
		 * @description The collection of stars in the rating.
		 * @type {Array}
		 */
		var stars = [];

		/**
		 * init
		 *
		 * @description Initializes the rating widget. Returns nothing.
		 */
		(function init() {
			if (!el) {
				throw Error('No element supplied.');
			}

			if (!maxRating) {
				throw Error('No max rating supplied.');
			}

			if (!currentRating) {
				currentRating = 0;
			}

			if (currentRating < 0 || currentRating > maxRating) {
				throw Error('Current rating is out of bounds.');
			}

			for (var i = 0; i < maxRating; i++) {
				var star = document.createElement('li');
				star.classList.add('c-rating__item');
				star.setAttribute('data-index', i);
				if (i < currentRating) {
					star.classList.add('is-active');
				}

				el.appendChild(star);
				stars.push(star);
				attachStarEvents(star);
			}
		})();

		/**
		 * iterate
		 *
		 * @description A simple iterator used to loop over the stars collection.
		 *   Returns nothing.
		 * @param {Array} collection The collection to be iterated
		 * @param {Function} callback The callback to run on items in the collection
		 */
		function iterate(collection, callback) {
			for (var i = 0; i < collection.length; i++) {
				var item = collection[i];
				callback(item, i);
			}
		}

		/**
		 * attachStarEvents
		 *
		 * @description Attaches events to each star in the collection. Returns
		 *   nothing.
		 * @param {HTMLElement} star The star element
		 */
		function attachStarEvents(star) {
			starMouseOver(star);
			starMouseOut(star);
			starClick(star);
		}

		/**
		 * starMouseOver
		 *
		 * @description The mouseover event for the star. Returns nothing.
		 * @param {HTMLElement} star The star element
		 */
		function starMouseOver(star) {
			star.addEventListener('mouseover', function (e) {
				iterate(stars, function (item, index) {
					if (index <= parseInt(star.getAttribute('data-index'))) {
						item.classList.add('is-active');
					}
					else {
						item.classList.remove('is-active');
					}
				});
			});
		}

		/**
		 * starMouseOut
		 *
		 * @description The mouseout event for the star. Returns nothing.
		 * @param {HTMLElement} star The star element
		 */
		function starMouseOut(star) {
			star.addEventListener('mouseout', function (e) {
				if (stars.indexOf(e.relatedTarget) === -1) {
					setRating(null, false);
				}
			});
		}

		/**
		 * starClick
		 *
		 * @description The click event for the star. Returns nothing.
		 * @param {HTMLElement} star The star element
		 */
		function starClick(star) {
			star.addEventListener('click', function (e) {
				e.preventDefault();
				setRating(parseInt(star.getAttribute('data-index')) + 1, true);
			});
		}

		/**
		 * setRating
		 *
		 * @description Sets and updates the currentRating of the widget, and runs
		 *   the callback if supplied. Returns nothing.
		 * @param {Number} value The number to set the rating to
		 * @param {Boolean} doCallback A boolean to determine whether to run the
		 *   callback or not
		 */
		function setRating(value, doCallback) {
			if (value && value < 0 || value > maxRating) {
				return;
			}

			if (doCallback === undefined) {
				doCallback = true;
			}

			currentRating = value || currentRating;

			iterate(stars, function (star, index) {
				if (index < currentRating) {
					star.classList.add('is-active');
				}
				else {
					star.classList.remove('is-active');
				}
			});

			if (callback && doCallback) {
				callback(getRating());
			}
		}

		/**
		 * getRating
		 *
		 * @description Gets the current rating.
		 * @return {Number} The current rating
		 */
		function getRating() {
			return currentRating;
		}

		/**
		 * Returns the setRating and getRating methods
		 */
		return {
			setRating: setRating,
			getRating: getRating
		};

	}

	/**
	 * Add to global namespace
	 */
	window.rating = rating;

})();
