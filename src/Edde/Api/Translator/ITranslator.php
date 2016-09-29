<?php
	declare(strict_types = 1);

	namespace Edde\Api\Translator;

	use Edde\Api\Deffered\IDeffered;

	/**
	 * Implementation of a translator.
	 */
	interface ITranslator extends IDeffered {
		/**
		 * register source of words
		 *
		 * @param IDictionary $dictionary
		 *
		 * @return ITranslator
		 */
		public function registerDictionary(IDictionary $dictionary): ITranslator;

		/**
		 * language can be set in a runtime
		 *
		 * @param string $language
		 *
		 * @return ITranslator
		 */
		public function setLanguage(string $language): ITranslator;

		/**
		 * try to translate a string
		 *
		 * @param string $id
		 * @param string|null $language
		 *
		 * @return string
		 */
		public function translate(string $id, string $language = null): string;
	}
