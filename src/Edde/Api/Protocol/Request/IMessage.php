<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol\Request;

	use Edde\Api\Protocol\IElement;

	/**
	 * Message is kind of request not requiring actual answer.
	 */
	interface IMessage extends IElement {
		/**
		 * return the requested "resource" name
		 *
		 * @return string
		 */
		public function getRequest(): string;
	}
