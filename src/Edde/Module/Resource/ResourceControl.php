<?php
	namespace Edde\Module\Resource;

	use Edde\Api\Resource\IResourceIndex;
	use Edde\Common\Control\AbstractControl;

	class ResourceControl extends AbstractControl {
		/**
		 * @var IResourceIndex
		 */
		protected $resourceIndex;

		final public function injectResourceIndex(IResourceIndex $resourceIndex) {
			$this->resourceIndex = $resourceIndex;
		}

		public function actionUpdate() {
			$this->resourceIndex->update();
			printf("Resources updated.\n");
		}
	}
