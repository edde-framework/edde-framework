<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IErrorControl;
	use Edde\Common\Control\AbstractControl;

	/**
	 * Only rethrows exception.
	 */
	class ExceptionErrorControl extends AbstractControl implements IErrorControl {
		public function exception(\Exception $e) {
			throw $e;
		}
	}
