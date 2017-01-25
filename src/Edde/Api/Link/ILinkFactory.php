<?php
	declare(strict_types=1);

	namespace Edde\Api\Link;

	use Edde\Api\Config\IConfigurable;

	/**
	 * Abstract tool for generating links from arbitrary input (strings, other classes, ...). This is useful for
	 * abstracting application from url's.
	 */
	interface ILinkFactory extends ILinkGenerator, IConfigurable {
		/**
		 * register link a generator
		 *
		 * @param ILinkGenerator $linkGenerator
		 *
		 * @return ILinkFactory
		 */
		public function registerLinkGenerator(ILinkGenerator $linkGenerator): ILinkFactory;
	}
