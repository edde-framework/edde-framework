<?php
	declare(strict_types = 1);

	namespace Edde\Api\Resource;

	use Edde\Api\Node\INode;
	use Edde\Api\Usable\IUsable;

	interface IResourceManager extends IUsable {
		/**
		 * @param IResourceHandler $resourceHandler
		 * @param bool $force === true override already registered handlers?
		 *
		 * @return IResourceManager
		 */
		public function registerResourceHandler(IResourceHandler $resourceHandler, bool $force = false): IResourceManager;

		/**
		 * @param IResource $resource
		 * @param string $mime
		 *
		 * @return IResourceHandler
		 */
		public function getHandler(IResource $resource, string $mime = null): IResourceHandler;

		/**
		 * IResource is created from the given url and then handler is selected based on a mime
		 *
		 * @param string $url
		 * @param string $mime override/specify mimetype
		 * @param INode $root
		 *
		 * @return INode
		 */
		public function handle(string $url, string $mime = null, INode $root = null): INode;

		/**
		 * same as handle only formally for a file
		 *
		 * @param string $file
		 * @param string|null $mime
		 * @param INode $root
		 *
		 * @return INode
		 */
		public function file(string $file, string $mime = null, INode $root = null): INode;

		/**
		 * @param IResource $resource
		 * @param string|null $mime
		 * @param INode $root
		 *
		 * @return INode
		 */
		public function resource(IResource $resource, string $mime = null, INode $root = null): INode;

		public function registerConverter(IConverter $converter): IResourceManager;

		/**
		 * magical method for generic data conversion; ideologically it is based on a mime type conversion, but identifiers can be arbitrary
		 *
		 * @param mixed $source generic input which will be converted in a generic output (defined by mime a target)
		 * @param string $mime generic identifier, it can be formal mime type or anything else (but there must be known converter)
		 * @param string $target target type of conversion
		 *
		 * @return mixed return converted source; result depends on mime+target combination
		 */
		public function convert($source, string $mime, string $target);
	}
