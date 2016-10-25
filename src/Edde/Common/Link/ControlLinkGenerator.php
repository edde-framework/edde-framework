<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Common\Deffered\DefferedTrait;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;

	class ControlLinkGenerator extends AbstractLinkGenerator {
		use LazyRequestTrait;
		use LazyRequestUrlTrait;
		use DefferedTrait;

		protected $regexp;

		public function link($generate, ...$parameterList) {
			$this->use();
			list($generate, $parameterList) = $this->list($generate, $parameterList);
			if (is_array($generate) === false || count($generate) !== 2) {
				return null;
			}
			list($control, $action) = $generate;
			if (class_exists($control = is_object($control) ? get_class($control) : $control) === false) {
				return null;
			}
			/** @var $match array */
			if (($match = StringUtils::match($action, '~^' . $this->regexp . '$~', true, true)) === null) {
				$match['context'] = $match['handle'] = $action;
			}
			if (isset($match['context'])) {
				if (isset($match['handle']) === false) {
					$match['handle'] = $match['context'];
				}
				switch ($match['context']) {
					case '$':
						$query = $this->requestUrl->getQuery();
						list($control, $match['context']) = explode('.', $query['context'] ?? $query['action']);
						break;
				}
				$parameterList['context'] = $control . '.' . $match['context'];
				$parameterList['handle'] = $control . '.' . $match['handle'];
			}
			if (isset($match['handle'])) {
				$parameterList['handle'] = $control . '.' . $match['handle'];
			} else {
				return null;
			}
			return Url::create()
				->setQuery(array_merge($this->request->getParameterList(), $parameterList))
				->getAbsoluteUrl();
		}

		protected function prepare() {
			$item = [
				'context',
				'handle',
			];
			foreach ($item as $v) {
				$this->regexp .= str_replace('$name', $v, '(($name=(?<$name>[a-zA-Z0-9$#@-]+)(\\.(?<$nameHandler>[a-zA-Z0-9-]+))?,?))?');
			}
		}
	}
