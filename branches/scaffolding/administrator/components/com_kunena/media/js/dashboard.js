window.addEvent('domready', function(){
	a = $('jxplugin-enable');
	if (a) {
		a.addEvent('hide', function(){
			this.getParent().setProperty('hidden', 'hidden');
			var mySlider = new Fx.Slide(this.getParent(), {duration: 300});
			mySlider.slideOut();
		});
		a.addEvent('click', function(){
			new Json.Remote('index.php?option=com_kunena&task=dashboard.enablePlugin&tmpl=component&protocol=json', {linkId: this.getProperty('id'), onComplete: function(response){
				if (response.error == false) {
					$(this.options.linkId).fireEvent('hide');
					$('system-message').fireEvent('check');
				} else {
					alert(response.message);
				}
			}}).send();

			return false;
		}, a);
		a.setProperty('href', 'javascript: void(0);');
	}

	sm = $('system-message');
	if (sm) {
		sm.addEvent('check', function(){
			open = 0;
			messages = this.getElements('li');
			for (i=0,n=messages.length;i < n; i++)
			{
				if (messages[i].getProperty('hidden') != 'hidden') {
					open++;
				}
			}
			if (open < 1) {
				var mySlider = new Fx.Slide(this, {duration: 200});
				mySlider.slideOut();
			}
		});
	}

	// lets handle the hide warning links
	function hideWarning(e) {
		new Json.Remote(this.getProperty('link')+'&protocol=json', {linkId: this.getProperty('id'), onComplete: function(response){
			if (response.error == false) {
				$(this.options.linkId).fireEvent('hide');
				$('system-message').fireEvent('check');
			} else {
				alert(response.message);
			}
		}}).send();
	}

	$$('a.hide-warning').each(function(a){
		a.setProperty('link', a.getProperty('href'));
		a.setProperty('href', 'javascript: void(0);');
		a.addEvent('hide', function(){
			this.getParent().setProperty('hidden', 'hidden');
			var mySlider = new Fx.Slide(this.getParent(), {duration: 300});
			mySlider.slideOut();
		});
		a.addEvent('click', hideWarning.bindWithEvent(a));
	});
});