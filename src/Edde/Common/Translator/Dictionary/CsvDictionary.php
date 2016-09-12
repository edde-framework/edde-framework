<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator\Dictionary;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Common\Translator\AbstractDictionary;

	class CsvDictionary extends AbstractDictionary implements ILazyInject {
		/**
		 * @var string
		 */
		protected $file;
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;
		/**
		 * @var string[]
		 */
		protected $dictionary;

		public function __construct(string $file) {
			$this->file = $file;
		}

		public function lazyResourceManager(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}

		public function translate(string $id, string $language) {
		}

		protected function prepare() {
			$source = $this->resourceManager->file($this->file);
		}
	}
