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
		 *
		 * @return INode
		 */
		public function handle(string $url, string $mime = null): INode;

		/**
		 * same as handle only formally for a file
		 *
		 * @param string $file
		 * @param string|null $mime
		 *
		 * @return INode
		 */
		public function file(string $file, string $mime = null): INode;
	}
