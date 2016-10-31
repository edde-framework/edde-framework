(function ($) {
	var xhrPool = [];
	var $document = $(document);
	$document.ajaxSend(function (e, jqXHR, options) {
		xhrPool.push(jqXHR);
	});
	$document.ajaxComplete(function (e, jqXHR, options) {
		xhrPool = $.grep(xhrPool, function (x) {
			return x != jqXHR
		});
	});
	var abort = function () {
		$.each(xhrPool, function (idx, jqXHR) {
			jqXHR.abort();
		});
	};
	var onBeforeUnload = window.onbeforeunload;
	window.onbeforeunload = function () {
		var result = onBeforeUnload ? onBeforeUnload() : undefined;
		if (result == undefined) {
			abort();
		}
		return result;
	}
})(jQuery);

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
			event = $.extend(true, $.Event(event), parameterList || {});
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
				dataType: 'json',
				cache: false
			}).fail(function (e) {
				Edde.Event.event('edde.on-ajax-fail', {
					error: e
				});
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

var $document = $(document);
$document.ready(function () {
	$document.on('click', '.button', function (event) {
		if (event.isDefaultPrevented()) {
			return;
		}
		var $this = $(this);
		if ($this.hasClass('disabled')) {
			return;
		}
		$this.addClass('disabled');
		Edde.Utils.execute($this.data('action'), Edde.Utils.crate($this.data('bind'))).always(function () {
			$this.removeClass('disabled');
		});
	});
});
