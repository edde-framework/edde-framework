function a($element) {
	$element.click(function () {
		if ($element.hasClass('disabled')) {
			return;
		}
		$element.addClass('disabled');
		Edde.Utils.execute($element.data('action'), Edde.Utils.crate($element.data('bind'))).always(function () {
			$element.removeClass('disabled');
		});
	});
}
