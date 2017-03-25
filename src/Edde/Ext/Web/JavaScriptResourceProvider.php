<?php
	declare(strict_types=1);

	namespace Edde\Ext\Web;

	use Edde\Api\Asset\LazyAssetDirectoryTrait;
	use Edde\Api\File\IDirectory;
	use Edde\Api\Resource\IResource;
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
		protected $styleSheetDirectory;

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, string $namespace = null, ...$parameters): IResource {
			$file = $this->styleSheetDirectory->file($name = $name . ($namespace ? $namespace . '-' . $name : ''));
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
			$this->styleSheetDirectory = $this->assetDirectory->directory('js');
			$this->styleSheetDirectory->realpath();
		}
	}
