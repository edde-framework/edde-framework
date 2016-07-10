<?php
	namespace Edde\Api\Http;

	use Edde\Api\Collection\IList;

	/**
	 * Explicit interface for http header list; missing array access interface is intentional.
	 */
	interface IHeaderList extends IList {
	}
