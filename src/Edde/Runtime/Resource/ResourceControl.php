<?php
	namespace Edde\Runtime\Resource;

	use Edde\Api\Resource\IResourceIndex;
	use Edde\Common\Control\AbstractControl;

	class ResourceControl extends AbstractControl {
		/**
		 * @var IResourceIndex
		 */
		protected $resourceIndex;

		public function actionUpdate() {
			$this->resourceIndex->update();
			echo 'ok';
		}

		protected function onPrepare() {
		}
	}
