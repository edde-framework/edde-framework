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
	class StyleSheetResourceProvider extends AbstractResourceProvider {
		use LazyAssetDirectoryTrait;
		/**
		 * @var IDirectory
		 */
		protected $styleSheetDirectory;

		/**
		 * @inheritdoc
		 */
		public function getResource(string $name, string $namespace = null, ...$parameters): IResource {
			if ($this->styleSheetDirectory === null) {
				throw new UnknownResourceException('Stylesheet directory has not been set up (or setup failed).');
			}
			$file = $this->styleSheetDirectory->file($name = $name . ($namespace ? $namespace . '-' . $name : ''));
			if ($file->isAvailable()) {
				return $file;
			}
			throw new UnknownResourceException(sprintf('Requested unknown stylesheet [%s].', $name));
		}

		/**
		 * @inheritdoc
		 */
		protected function handleSetup() {
			parent::handleSetup();
			try {
				$styleSheetDirectory = $this->assetDirectory->directory('css');
				$styleSheetDirectory->realpath();
				$this->styleSheetDirectory = $styleSheetDirectory;
			} catch (RealPathException $exception) {
			}
		}
	}
