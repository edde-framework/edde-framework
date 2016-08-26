<?php
	declare(strict_types = 1);

	namespace Edde\Common\Web;

	use Edde\Api\File\ITempDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Resource\Storage\IFileStorage;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Resource\ResourceList;
	use Edde\Common\Template\AbstractMacro;
	use Edde\Common\Usable\UsableTrait;

	class JavaScriptCompiler extends ResourceList implements IJavaScriptCompiler {
		use UsableTrait;
		use CacheTrait;

		/**
		 * @var IFileStorage
		 */
		protected $fileStorage;
		/**
		 * @var ITempDirectory
		 */
		protected $tempDirectory;

		static public function macro() {
			return new class extends AbstractMacro {
				public function __construct() {
					parent::__construct(['js']);
				}

				public function run(INode $root, ICompiler $compiler) {
					$destination = $compiler->getDestination();
					$destination->write(sprintf("\t\t\t\$this->javaScriptCompiler->addFile('%s');\n", $compiler->file($root->getAttribute('src'))));
				}
			};
		}

		public function lazyFileStorage(IFileStorage $fileStorage) {
			$this->fileStorage = $fileStorage;
		}

		public function lazyTempDirectory(ITempDirectory $tempDirectory) {
			$this->tempDirectory = $tempDirectory;
		}

		public function compile(IResourceList $resourceList) {
			$this->use();
			$content = [];
			if (($resource = $this->cache->load($cacheId = $resourceList->getResourceName())) === null) {
				foreach ($resourceList as $resource) {
					$current = $resource->get();
					$content[] = $current;
				}
				$this->cache->save($cacheId, $resource = $this->fileStorage->store($this->tempDirectory->save($resourceList->getResourceName() . '.js', implode(";\n", $content))));
			}
			return $resource;
		}

		protected function prepare() {
		}
	}
