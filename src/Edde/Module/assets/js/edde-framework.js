var Edde = {
	execute: function (control, action, parameterList) {
		return $.post('?control=' + control + '&action=' + action, parameterList || {}).fail(function () {
			alert('General server error; this should be fixed by a developer.');
		}).done(function (data) {
			if (data.redirect) {
				window.location.replace(data.redirect);
			}
			if (data.selector) {
				$.each(data.selector, function (selector, value) {
					switch (value.action) {
						case 'replace':
							$(selector).replaceWith(value.source);
							break;
					}
				});
			}
		});
	}
};

$(document).ready(function () {
	$(document).on('click', '.edde-clickable:not(.disabled)', function () {
		var $this = $(this);
		$this.addClass('disabled');
		Edde.execute($this.data('control'), $this.data('action')).always(function () {
			$this.removeClass('disabled');
		});
	});
});
