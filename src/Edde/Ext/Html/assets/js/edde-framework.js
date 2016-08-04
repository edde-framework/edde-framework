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
							$(selector).show();
							break;
					}
				});
			}
			Edde.bind();
		});
	},
	bind: function () {
		$('.edde-text-input').each(function () {
			this.getValue = function () {
				return $(this).val();
			};
		});
		$('.edde-hide-on-click').on('click', function () {
			$(this).hide();
		});
	},
	crate: function (id) {
		var crate = {};
		if (id) {
			$('#' + id).find('.edde-value').each(function () {
				var $this = $(this);
				var dataClass = $this.data('class');
				crate[dataClass] = crate[dataClass] ? crate[dataClass] : {};
				crate[dataClass][$this.data('property')] = this.getValue();
			});
		}
		return crate;
	}
};

$(document).ready(function () {
	Edde.bind();
	$(document).on('click', '.edde-clickable:not(.disabled)', function () {
		var $this = $(this);
		$this.addClass('disabled');
		Edde.execute($this.data('control'), $this.data('action'), Edde.crate($this.data('bind'))).always(function () {
			$this.removeClass('disabled');
		});
	});
});
