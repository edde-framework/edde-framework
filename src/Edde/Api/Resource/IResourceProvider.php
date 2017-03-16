<?php
	declare(strict_types=1);

	namespace Edde\Api\Resource;

	use Edde\Api\Config\IConfigurable;

	interface IResourceProvider extends IConfigurable {
		/**
		 * request resource by the name
		 *
		 * @param string $name
		 *
		 * @return IResource|null
		 */
		public function getResource(string $name);
	}
