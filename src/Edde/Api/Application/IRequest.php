<?php
	declare(strict_types=1);

	namespace Edde\Api\Application;

	use Edde\Api\Converter\IContent;

	/**
	 * General application request (it should not be necessarily be handled by an application).
	 */
	interface IRequest {
		/**
		 * return request id
		 *
		 * @return string
		 */
		public function getId(): string;

		/**
		 * return current control
		 *
		 * @return string
		 */
		public function getControl(): string;

		/**
		 * return action to be (hopefully) executed
		 *
		 * @return string
		 */
		public function getAction(): string;

		/**
		 * return current list of parameters of the request
		 *
		 * @return array
		 */
		public function getParameterList(): array;

		/**
		 * return content; if there is target specified, conversion will be executed
		 *
		 * @param array $targetList
		 *
		 * @return IContent|mixed
		 */
		public function getContent(array $targetList = null);
	}
