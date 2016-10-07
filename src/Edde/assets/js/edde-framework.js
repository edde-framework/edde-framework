var Edde = {
	Event: {
		listen: function (event, handler) {
			$(document).on(event, handler);
		},
		event: function (event) {
			$(document).trigger(event);
		}
	},
	Utils: {
		redirect: function (url) {
			Edde.Event.event('edde.redirect');
			window.location.href = url;
		},
		class: function (name, func) {
			setTimeout(function () {
				$('[data-class="' + name + '"]').each(function (i, element) {
					if (element.edde) {
						return;
					}
					if (typeof func === 'function') {
						func.call(element, ($(element)));
						return;
					}
					$.extend(true, element, func);
					element.edde = true;
				});
			}, 0);
		},
		execute: function (url, parameterList) {
			Edde.Event.event('edde.on-ajax');
			return $.ajax({
				url: url,
				method: 'POST',
				data: parameterList ? JSON.stringify(parameterList) : {},
				timeout: 10000,
				contentType: 'application/json',
				dataType: 'json'
			}).fail(function (e) {
				Edde.Event.event('edde.on-ajax-fail');
				console.log(e);
				alert('General server error; this should be fixed by a developer.');
			}).done(function (data) {
				Edde.Event.event('edde.on-ajax-done');
				if (data.redirect) {
					Edde.Utils.redirect(data.redirect);
					return;
				}
				if (data.javaScript) {
					$.each(data.javaScript, function (i, src) {
						$.getScript(src);
					});
				}
				if (data.styleSheet) {
					$.each(data.styleSheet, function (i, src) {
						if ($('head link[href="' + src + '"]').length) {
							return;
						}
						$('<link/>', {rel: 'stylesheet', href: src}).appendTo('head');
					});
				}
				if (data.selector) {
					$.each(data.selector, function (selector, value) {
						switch (value.action) {
							case 'replace':
								$(selector).replaceWith(value.source);
								break;
							case 'add':
								$(selector).append(value.source);
								break;
						}
					});
				}
			}).always(function () {
				Edde.Event.event('edde.on-ajax-always');
			});
		},
		crate: function (id) {
			var crate = null;
			if (id) {
				crate = {};
				$('#' + id + '[data-schema], #' + id + ' *[data-schema]').each(function () {
					var $this = $(this);
					var dataClass = $this.data('schema');
					crate[dataClass] = crate[dataClass] ? crate[dataClass] : {};
					crate[dataClass][$this.data('property')] = this.getValue();
				});
			}
			return crate;
		}
	}
};
