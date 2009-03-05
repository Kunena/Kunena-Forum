
window.addEvent('domready', function(){
	actions = $$('a.move_up');
	actions.merge($$('a.move_down'));
	actions.merge($$('a.grid_true'));
	actions.merge($$('a.grid_false'));
	actions.merge($$('a.grid_trash'));

	actions.each(function(a){
		a.addEvent('click', function(){
			args = Json.evaluate(this.rel);
			listItemTask(args.id, args.task);
		});
	});
});