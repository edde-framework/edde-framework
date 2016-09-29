<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator;

	use Edde\Api\Translator\IDictionary;
	use Edde\Api\Translator\ITranslator;
	use Edde\Api\Translator\TranslatorException;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Deffered\AbstractDeffered;

	/**
	 * General class for translations support.
	 */
	class Translator extends AbstractDeffered implements ITranslator {
		use CacheTrait;
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
		 */
		public function registerDictionary(IDictionary $dictionary): ITranslator {
			$this->dictionaryList[] = $dictionary;
			return $this;
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
		 * @throws TranslatorException
		 */
		public function translate(string $id, string $language = null): string {
			$this->use();
			if (($language = $language ?: $this->language) === null) {
				throw new TranslatorException('Cannot use translator without set language.');
			}
			if (($string = $this->cache->load($id . $language)) !== null) {
				return $string;
			}
			foreach ($this->dictionaryList as $dictionary) {
				if (($string = $dictionary->translate($id, $language)) !== null) {
					return $this->cache->save($id . $language, $string);
				}
			}
			throw new TranslatorException(sprintf('Cannot translate [%s]; the given id is not available in no dictionary.', $id));
		}

		protected function prepare() {
			if (empty($this->dictionaryList)) {
				throw new TranslatorException('Translator needs at least one dictionary. Or The God will kill one cute devil kitten!');
			}
		}
	}
