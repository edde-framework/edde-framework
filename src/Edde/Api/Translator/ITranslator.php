<?php
	declare(strict_types = 1);

	namespace Edde\Api\Translator;

	use Edde\Api\Deffered\IDeffered;

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
		public function setLanguage($language): ITranslator;

		/**
		 * try to translate a string
		 *
		 * @param string $id
		 * @param array $parameterList
		 * @param string|null $language
		 *
		 * @return string
		 */
		public function translate(string $id, array $parameterList = [], string $language = null): string;

		/**
		 * internally use sprintf with combination of parameterList
		 *
		 * @param string $id
		 * @param array $parameterList
		 * @param string|null $language
		 *
		 * @return string
		 */
		public function translatef(string $id, array $parameterList = null, string $language = null): string;
	}
