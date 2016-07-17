var Edde = {
	execute: function (control, action, parameterList) {
		return $.post('?control=' + control + '&action=' + action, parameterList || {}).fail(function () {
			alert('General server error; this should be fixed by a developer.');
		}).done(function (data) {
			$.each(data, function (selector, value) {
				switch (value.action) {
					case 'replace':
						$(selector).replaceWith(value.source);
						break;
				}
			});
		});
	}
};

$(document).ready(function () {
	$(document).on('click', '.edde-clickable', function () {
		var $this = $(this);
		Edde.execute($this.data('control'), $this.data('action'));
	});
});
