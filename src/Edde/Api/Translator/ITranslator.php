<?php
	declare(strict_types = 1);

	namespace Edde\Api\Translator;

	use Edde\Api\Usable\IUsable;

	interface ITranslator extends IUsable {
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
		public function setLanguage($language): ITranslator;

		/**
		 * try to translate a string
		 *
		 * @param string $id
		 *
		 * @return string
		 */
		public function translate(string $id): string;
	}
