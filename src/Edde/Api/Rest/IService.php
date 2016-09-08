<?php
	declare(strict_types = 1);

	namespace Edde\Api\Rest;

	use Edde\Api\Control\IControl;
	use Edde\Api\Url\IUrl;

	/**
	 * Rest service handler.
	 */
	interface IService extends IControl {
		/**
		 * return true, if this REST service can handle the given URL
		 *
		 * @param IUrl $url
		 *
		 * @return bool
		 */
		public function match(IUrl $url): bool;
	}
