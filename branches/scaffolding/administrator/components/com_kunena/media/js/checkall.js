function checkAll(checkbox, stub) {
	c = 0;
	for (i = 0, n = checkbox.form.elements.length; i < n; i++) {
		e = checkbox.form.elements[i];
		if (e.type == checkbox.type) {
			if ((stub && e.name.indexOf(stub) == 0) || !stub) {
				e.checked = checkbox.checked;
				c += (e.checked == true ? 1 : 0);
			}
		}
	}
	if (checkbox.form.boxchecked) {
		checkbox.form.boxchecked.value = c;
	}
	return true;
}