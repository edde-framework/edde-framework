<?php
	declare(strict_types = 1);

	namespace Edde\Api\Link;

	use Edde\Api\Url\IUrl;

	/**
	 * Proprietary link generator with tight relation to the edde-application package.
	 */
	interface ILinkFactory extends ILinkGenerator {
		/**
		 * generate uri from the given string
		 *
		 * @param string $link
		 *
		 * @return IUrl
		 */
		public function link($link);

		/**
		 * generate link to the given class (for example to a Presenter or so)
		 *
		 * @param mixed $class
		 * @param string $method
		 * @param array $parameterList
		 *
		 * @return IUrl
		 */
		public function linkTo($class, $method, array $parameterList = []);
	}
