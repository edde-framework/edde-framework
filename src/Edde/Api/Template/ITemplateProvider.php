<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	use Edde\Api\Resource\IResource;

	/**
	 * Templates are "loaded" against a name. This bitch is responsible for translating the name to the resource.
	 */
	interface ITemplateProvider {
		/**
		 * try to get the resource for the given template name
		 *
		 * @return IResource|null
		 */
		public function getResource(string $name);
	}
