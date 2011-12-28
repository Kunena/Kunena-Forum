/*
---
description:     LazyLoad

authors:
  - David Walsh (http://davidwalsh.name)

license:
  - MIT-style license

requires:
  core/1.2.1:   "*"

provides:
  - LazyLoad
...
*/
var LazyLoad = new Class({

	Implements: [Options,Events],

	/* additional options */
	options: {
		range: 200,
		elements: "img",
		container: window,
		mode: "vertical",
		realSrcAttribute: "data-src",
		useFade: true
	},

	/* initialize */
	initialize: function(options) {
		
		// Set the class options
		this.setOptions(options);
		
		// Elementize items passed in
		this.container = document.id(this.options.container);
		this.elements = document.id(this.container == window ? document.body : this.container).getElements(this.options.elements);
		
		// Set a variable for the "highest" value this has been
		this.largestPosition = 0;
		
		// Figure out which axis to check out
		var axis = (this.options.mode == "vertical" ? "y": "x");
		
		// Calculate the offset
		var offset = (this.container != document.body && this.container != document.body ? this.container : "");

		// Find elements remember and hold on to
		this.elements = this.elements.filter(function(el) {
			// Make opacity 0 if fadeIn should be done
			if(this.options.useFade) el.setStyle("opacity", 0);
			// Get the image position
			var elPos = el.getPosition(offset)[axis];
			// If the element position is within range, load it
			if(elPos < this.container.getSize()[axis] + this.options.range) {
				this.loadImage(el);
				return false;
			}
			return true;
		},this);
		
		// Create the action function that will run on each scroll until all images are loaded
		var action = function(e) {
			
			// Get the current position
			var cpos = this.container.getScroll()[axis];
			
			// If the current position is higher than the last highest
			if(cpos > this.largestPosition) {
				
				// Filter elements again
				this.elements = this.elements.filter(function(el) {
					
					// If the element is within range...
					if((cpos + this.options.range + this.container.getSize()[axis]) >= el.getPosition(offset)[axis]) {
						
						// Load the image!
						this.loadImage(el);
						return false;
					}
					return true;
					
				},this);
				
				// Update the "highest" position
				this.largestPosition = cpos;
			}
			
			// relay the class" scroll event
			this.fireEvent("scroll");
			
			// If there are no elements left, remove the action event and fire complete
			if(!this.elements.length) {
				this.container.removeEvent("scroll", action);
				this.fireEvent("complete");
			}
			
		}.bind(this);
		
		// Add scroll listener
		this.container.addEvent("scroll", action);
	},
	loadImage: function(image) {
		// Set load event for fadeIn
		if(this.options.useFade) {
			image.addEvent("load", function(){
				image.fade(1);
			});
		}
		// Set the SRC
		image.set("src", image.get(this.options.realSrcAttribute));
		// Fire the image load event
		this.fireEvent("load", [image]);
	}
});