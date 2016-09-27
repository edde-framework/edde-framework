<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator;

	use Edde\Api\Translator\IDictionary;
	use Edde\Common\Deffered\AbstractDeffered;

	abstract class AbstractDictionary extends AbstractDeffered implements IDictionary {
		/**
		 * @var string[]
		 */
		protected $translationList = [];

		public function translatef(string $id, array $parameterList = null, string $language) {
			if (($translation = $this->translate($id, [], $language)) === null) {
				return null;
			}
			return $parameterList ? vsprintf($translation, $parameterList) : $translation;
		}

		public function translate(string $id, array $parameterList = [], string $language) {
			$this->use();
			if (isset($this->translationList[$language][$id]) === false) {
				return null;
			}
			$parameters = [];
			foreach ($parameterList as $k => $v) {
				$parameters['{' . $k . '}'] = is_callable($v) ? call_user_func($v) : $v;
			}
			return str_replace(array_keys($parameters), array_values($parameters), $this->translationList[$language][$id]);
		}
	}
