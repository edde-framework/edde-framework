<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator;

	use Edde\Api\Translator\IDictionary;
	use Edde\Api\Translator\ITranslator;
	use Edde\Api\Translator\TranslatorException;
	use Edde\Common\Usable\AbstractUsable;

	class Translator extends AbstractUsable implements ITranslator {
		/**
		 * @var IDictionary[]
		 */
		protected $dictionaryList = [];
		/**
		 * @var string
		 */
		protected $language;

		public function translate(string $id): string {
			$this->use();
			foreach ($this->dictionaryList as $dictionary) {
				if (($string = $dictionary->translate($id, $this->language)) !== null) {
					return $string;
				}
			}
			throw new TranslatorException(sprintf('Cannot translate [%s]; the given id is not available in no dictionary.', $id));
		}

		public function setLanguage($language): ITranslator {
			$this->language = $language;
			return $this;
		}

		public function registerDitionary(IDictionary $dictionary): ITranslator {
			$this->dictionaryList[] = $dictionary;
			return $this;
		}

		protected function prepare() {
			if ($this->language === null) {
				throw new TranslatorException('Cannot use translator without set language.');
			}
			if (empty($this->dictionaryList)) {
				throw new TranslatorException('Translator needs at least one dictionary. Or The God will kill one cute devil kitten!');
			}
		}
	}
