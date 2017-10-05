<?php
	namespace Edde\Common\Application;

	use Edde\Api\Application\IApplication;
	use Edde\Common\Container\AbstractService;

	class ApplicationService extends AbstractService {
		/**
		 * @return IApplication
		 */
		static public function get() {
			/** @noinspection PhpIncompatibleReturnTypeInspection */
			return self::instance();
		}
	}
