<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\File\IFile;
	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Common\Deffered\DefferedTrait;

	/**
	 * JavaScript "minifier" resource compiler.
	 */
	class JavaScriptCompiler extends AbstractCompiler implements IJavaScriptCompiler {
		use DefferedTrait;
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		/**
		 * @param ITempDirectory $tempDirectory
		 */
		public function lazyTempDirectory(ITempDirectory $tempDirectory) {
			$this->tempDirectory = $tempDirectory;
		}

		public function compile(IResourceList $resourceList): IFile {
			$this->use();
			$content = [];
			if (($file = $this->cache->load($cacheId = $resourceList->getResourceName())) === null) {
				foreach ($resourceList as $resource) {
					$content[] = $resource->get();
				}
				$this->cache->save($cacheId, $file = $this->assetStorage->store($this->tempDirectory->save($resourceList->getResourceName() . '.js', implode(";\n", $content))));
			}
			return $file;
		}
	}
