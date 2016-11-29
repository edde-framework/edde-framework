<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator;

	use Edde\Api\Translator\IDictionary;
	use Edde\Common\AbstractObject;

	/**
	 * Common dictionary implementation.
	 */
	abstract class AbstractDictionary extends AbstractObject implements IDictionary {
		/**
		 * @var string[]
		 */
		protected $translationList = [];

		/**
		 * @inheritdoc
		 */
		public function translate(string $id, string $language) {
			$this->use();
			if (isset($this->translationList[$language][$id]) === false) {
				return null;
			}
			return $this->translationList[$language][$id];
		}
	}
