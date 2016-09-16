<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator;

	use Edde\Api\Translator\IDictionary;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractDictionary extends AbstractUsable implements IDictionary {
		/**
		 * @var string[]
		 */
		protected $translationList = [];

		public function translate(string $id, string $language) {
			$this->use();
			if (isset($this->translationList[$language][$id]) === false) {
				return null;
			}
			return $this->translationList[$language][$id];
		}
	}
