<?php
	declare(strict_types = 1);

	namespace Edde\Api\Translator;

	/**
	 * Formal source for all words.
	 */
	interface IDictionary {
		/**
		 * try to translate a word; if word is not found, null should be returned
		 *
		 * @param string $id
		 * @param array $parameterList
		 * @param string $language requested language
		 *
		 * @return string|null
		 */
		public function translate(string $id, array $parameterList = [], string $language);

		/**
		 * @param string $id
		 * @param array|null $parameterList
		 * @param string $language
		 *
		 * @return string|null
		 */
		public function translatef(string $id, array $parameterList = null, string $language);
	}
