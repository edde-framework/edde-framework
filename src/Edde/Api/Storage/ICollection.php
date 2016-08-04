<?php
	declare(strict_types = 1);

	namespace Edde\Api\Storage;

	use IteratorAggregate;

	/**
	 * Collection of IStorable is the result of a query.
	 */
	interface ICollection extends IteratorAggregate {
	}
