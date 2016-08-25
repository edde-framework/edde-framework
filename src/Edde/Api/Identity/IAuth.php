<?php
	declare(strict_types = 1);

	namespace Edde\Api\Identity;

	use Edde\Api\Usable\IUsable;

	/**
	 * @internal interface for common stuff for Authorizator and Authentificator
	 */
	interface IAuth extends IUsable {
		/**
		 * name of auth method
		 *
		 * @return string
		 */
		public function getName(): string;
	}
