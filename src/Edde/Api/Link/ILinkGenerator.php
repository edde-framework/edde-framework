<?php
	namespace Edde\Api\Link;

	/**
	 * Abstract tool for generating links from arbitrary input (strings, other classes, ...). This is useful for
	 * abstracting application from url's.
	 */
	interface ILinkGenerator {
		public function generate($generate);
	}
