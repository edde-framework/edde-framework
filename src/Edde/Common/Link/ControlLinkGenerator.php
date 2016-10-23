<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;

	class ControlLinkGenerator extends AbstractLinkGenerator {
		use LazyRequestTrait;

		public function link($generate, ...$parameterList) {
			list($generate, $parameterList) = $this->list($generate, $parameterList);
			if (is_array($generate) === false || count($generate) !== 2) {
				return null;
			}
			list($control, $action) = $generate;
			if (class_exists($control = is_object($control) ? get_class($control) : $control) === false) {
				return null;
			}
			if (($match = StringUtils::match($action, '~^(\$(?<context>[a-zA-Z0-9-]+))?(#(?<handle>[a-zA-Z0-9-]+))?(@(?<action>[a-zA-Z0-9-]+))?$~', true)) === null) {
				return null;
			}
			if (isset($match['context'], $match['handle'])) {
				$parameterList['context'] = $control . '.' . $match['context'];
				$parameterList['handle'] = $control . '.' . $match['handle'];
			} else if ($match['action']) {
				$parameterList['action'] = $control . '.' . $match['action'];
			} else if ($match['handle']) {
				$parameterList['handle'] = $control . '.' . $match['handle'];
			} else {
				return null;
			}
			return Url::create()
				->setQuery(array_merge($this->request->getParameterList(), $parameterList))
				->getAbsoluteUrl();
		}
	}
