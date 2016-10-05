var Edde = {
	Event: {
		listen: function (event, handler) {
			$(document).on(event, handler);
		},
		/**
		 *
		 * @param {string} event
		 * @param {object} [parameterList]
		 * @returns {jQuery.Event}
		 */
		event: function (event, parameterList) {
			event = $.extend(true, $.event(event), parameterList || {});
			$(document).trigger(event);
			return event;
		}
	},
	Utils: {
		redirect: function (url) {
			var event = Edde.Event.event('edde.redirect', {
				url: url,
			});
			if (event.isDefaultPrevented()) {
				return;
			}
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
		update: function (update) {
			if (update.redirect) {
				Edde.Utils.redirect(update.redirect);
				return;
			}
			if (update.javaScript) {
				$.each(update.javaScript, function (i, src) {
					$.getScript(src);
				});
			}
			if (update.styleSheet) {
				$.each(update.styleSheet, function (i, src) {
					if ($('head link[href="' + src + '"]').length) {
						return;
					}
					$('<link/>', {rel: 'stylesheet', href: src}).appendTo('head');
				});
			}
			if (update.selector) {
				$.each(update.selector, function (selector, value) {
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
		},
		execute: function (url, parameterList) {
			var event = Edde.Event.event('edde.on-ajax', {
				url: url,
				parameterList: parameterList
			});
			if (event.isDefaultPrevented()) {
				return;
			}
			return $.ajax({
				url: url,
				method: 'POST',
				data: parameterList ? JSON.stringify(parameterList) : {},
				timeout: 10000,
				contentType: 'application/json',
				dataType: 'json'
			}).fail(function (e) {
				Edde.Event.event('edde.on-ajax-fail', {
					error: e
				});
				console.log(e);
			}).done(function (data) {
				Edde.Event.event('edde.on-ajax-done', {
					data: data
				});
				Edde.Utils.update(data);
			}).always(function () {
				Edde.Event.event('edde.on-ajax-always');
			});
		},
		crate: function (id) {
			var crate = null;
			if (id) {
				crate = {};
				$('#' + id).find('[data-schema]').each(function () {
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
