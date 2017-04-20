<?php
	declare(strict_types=1);

	namespace Edde\Ext\Web;

	use Edde\Api\Asset\LazyAssetDirectoryTrait;
	use Edde\Api\File\IDirectory;
	use Edde\Api\Resource\IResource;
	use Edde\Common\File\RealPathException;
	use Edde\Common\Resource\AbstractResourceProvider;
	use Edde\Common\Resource\UnknownResourceException;

	/**
	 * Standard resource provider based on IAssetDirectory.
	 */
	class JavaScriptResourceProvider extends AbstractResourceProvider {
		use LazyAssetDirectoryTrait;
		/**
		 * @var IDirectory
		 */
		protected $javaScriptDirectory;

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, string $namespace = null, ...$parameters): IResource {
			if ($this->javaScriptDirectory === null) {
				throw new UnknownResourceException('Javascript directory has not been set up (or setup failed).');
			}
			$file = $this->javaScriptDirectory->file($name = $name . ($namespace ? $namespace . '-' . $name : ''));
			if ($file->isAvailable()) {
				return $file;
			}
			throw new UnknownResourceException(sprintf('Requested unknown javascript [%s].', $name));
		}

		/**
		 * @inheritdoc
		 */
		protected function handleSetup() {
			parent::handleSetup();
			try {
				$javaScriptDirectory = $this->assetDirectory->directory('js');
				$javaScriptDirectory->realpath();
				$this->javaScriptDirectory = $javaScriptDirectory;
			} catch (RealPathException $exception) {
			}
		}
	}
