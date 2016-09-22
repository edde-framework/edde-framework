<?php
	namespace Edde\Common\Template\Helper;

	use Edde\Api\IAssetsDirectory;
	use Edde\Common\Template\AbstractHelper;

	class EddeAssetHelper extends AbstractHelper {
		/**
		 * @var IAssetsDirectory
		 */
		protected $assetsDirectory;

		public function lazyAssetsDirectory(IAssetsDirectory $assetsDirectory) {
			$this->assetsDirectory = $assetsDirectory;
		}

		public function helper($value, ...$parameterList) {
			if (strpos($value, 'edde://') === false) {
				return $value;
			}
			return $this->assetsDirectory->filename(str_replace('edde://', '', $value));
		}
	}
