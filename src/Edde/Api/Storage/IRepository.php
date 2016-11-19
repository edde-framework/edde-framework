<?php
	declare(strict_types = 1);

	namespace Edde\Api\Storage;

	use Edde\Api\Crate\ICrate;

	/**
	 * Repository is simple type of storage, intended to be used as storage endpoint for
	 * services (for example user service will be extended from this interface).
	 */
	interface IRepository {
		/**
		 * try to store the given crate
		 *
		 * @param ICrate $crate
		 *
		 * @return IRepository
		 */
		public function store(ICrate $crate): IRepository;

		/**
		 * return bound query
		 *
		 * @return IBoundQuery
		 */
		public function query(): IBoundQuery;
	}
