<?php
	namespace Edde\Common\Web;

	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Resource\Storage\IFileStorage;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Common\AbstractObject;

	class JavaScriptCompiler extends AbstractObject implements IJavaScriptCompiler {
		/**
		 * @var IFileStorage
		 */
		protected $fileStorage;
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		/**
		 * @param IFileStorage $fileStorage
		 * @param ITempDirectory $tempDirectory
		 */
		public function __construct(IFileStorage $fileStorage, ITempDirectory $tempDirectory) {
			$this->fileStorage = $fileStorage;
			$this->tempDirectory = $tempDirectory;
		}

		public function compile(IResourceList $resourceList) {
			$content = [];
			foreach ($resourceList->getResourceList() as $resource) {
				$current = $resource->get();
				$content[] = $current;
			}
			return $this->fileStorage->store($this->tempDirectory->save($resourceList->getResourceName() . '.js', implode(";\n", $content)));
		}
	}
