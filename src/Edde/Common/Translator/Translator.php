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
		 * @var IDictionary[][]
		 */
		protected $dictionaryList = [];
		/**
		 * @var string
		 */
		protected $language;
		/**
		 * @var \SplStack
		 */
		protected $scopeStack;

		/**
		 * @inheritdoc
		 */
		public function registerDictionary(IDictionary $dictionary, string $scope = null): ITranslator {
			$this->dictionaryList[$scope][] = $dictionary;
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
		 */
		public function pushScope(string $scope = null): ITranslator {
			$this->use();
			$this->scopeStack->push($scope);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function popScope(): ITranslator {
			$this->use();
			$this->scopeStack->pop();
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws TranslatorException
		 */
		public function translate(string $id, string $scope = null, string $language = null): string {
			$this->use();
			if (($language = $language ?: $this->language) === null) {
				throw new TranslatorException('Cannot use translator without set language.');
			}
			if (($string = $this->cache->load($cacheId = ($id . $language . ($scope = $scope ?: $this->scopeStack->top())))) !== null) {
				return $string;
			}
			if (isset($this->dictionaryList[$scope]) === false) {
				throw new TranslatorException(sprintf('Scope [%s] has no dictionaries.', $scope));
			}
			$dictionaryList = $this->dictionaryList[$scope];
			foreach ($dictionaryList as $dictionary) {
				if (($string = $dictionary->translate($id, $language)) !== null) {
					return $this->cache->save($cacheId, $string);
				}
			}
			throw new TranslatorException(sprintf('Cannot translate [%s]; the given id is not available in no dictionary.', $id));
		}

		protected function prepare() {
			$this->scopeStack = new \SplStack();
			$this->scopeStack->push(null);
			if (empty($this->dictionaryList)) {
				throw new TranslatorException('Translator needs at least one dictionary. Or The God will kill one cute devil kitten!');
			}
		}
	}
