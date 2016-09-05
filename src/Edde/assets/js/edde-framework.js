var Edde = {
	Utils: {
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
			return $.post(url, parameterList || {}).fail(function () {
				alert('General server error; this should be fixed by a developer.');
			}).done(function (data) {
				if (data.redirect) {
					window.location.replace(data.redirect);
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
			});
		},
		crate: function (id) {
			var crate = {};
			if (id) {
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
