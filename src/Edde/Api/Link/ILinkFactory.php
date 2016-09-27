<?php
	declare(strict_types = 1);

	namespace Edde\Api\Link;

	use Edde\Api\Usable\IDeffered;

	/**
	 * Abstract tool for generating links from arbitrary input (strings, other classes, ...). This is useful for
	 * abstracting application from url's.
	 */
	interface ILinkFactory extends ILinkGenerator, IDeffered {
		/**
		 * register link a generator
		 *
		 * @param ILinkGenerator $lingGenerator
		 *
		 * @return ILinkFactory
		 */
		public function registerLinkGenerator(ILinkGenerator $lingGenerator): ILinkFactory;
	}
