<?php
	declare(strict_types = 1);

	namespace Edde\Api\Link;

	use Edde\Api\Usable\IUsable;

	/**
	 * Abstract tool for generating links from arbitrary input (strings, other classes, ...). This is useful for
	 * abstracting application from url's.
	 */
	interface ILinkFactory extends ILinkGenerator, IUsable {
		/**
		 * register link a generator
		 *
		 * @param ILinkGenerator $lingGenerator
		 *
		 * @return ILinkFactory
		 */
		public function registerLinkGenerator(ILinkGenerator $lingGenerator): ILinkFactory;
	}
