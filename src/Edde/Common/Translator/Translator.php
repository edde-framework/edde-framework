<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator;

	use Edde\Api\Translator\IDictionary;
	use Edde\Api\Translator\ITranslator;
	use Edde\Api\Translator\TranslatorException;
	use Edde\Common\Deffered\AbstractDeffered;

	/**
	 * General class for translations support.
	 */
	class Translator extends AbstractDeffered implements ITranslator {
		/**
		 * @var IDictionary[]
		 */
		protected $dictionaryList = [];
		/**
		 * @var string
		 */
		protected $language;

		/**
		 * @inheritdoc
		 * @throws TranslatorException
		 */
		public function translate(string $id, array $parameterList = [], string $language = null): string {
			$this->use();
			if (($language = $language ?: $this->language) === null) {
				throw new TranslatorException('Cannot use translator without set language.');
			}
			foreach ($this->dictionaryList as $dictionary) {
				if (($string = $dictionary->translate($id, $parameterList, $language)) !== null) {
					return $string;
				}
			}
			throw new TranslatorException(sprintf('Cannot translate [%s]; the given id is not available in no dictionary.', $id));
		}

		/**
		 * @inheritdoc
		 * @throws TranslatorException
		 */
		public function translatef(string $id, array $parameterList = null, string $language = null): string {
			$this->use();
			if (($language = $language ?: $this->language) === null) {
				throw new TranslatorException('Cannot use translator without set language.');
			}
			foreach ($this->dictionaryList as $dictionary) {
				if (($string = $dictionary->translatef($id, $parameterList, $language)) !== null) {
					return $string;
				}
			}
			throw new TranslatorException(sprintf('Cannot translate [%s]; the given id is not available in no dictionary.', $id));
		}

		/**
		 * @inheritdoc
		 */
		public function setLanguage(string $language): ITranslator {
			$this->language = $language;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerDictionary(IDictionary $dictionary): ITranslator {
			$this->dictionaryList[] = $dictionary;
			return $this;
		}

		protected function prepare() {
			if (empty($this->dictionaryList)) {
				throw new TranslatorException('Translator needs at least one dictionary. Or The God will kill one cute devil kitten!');
			}
		}
	}
