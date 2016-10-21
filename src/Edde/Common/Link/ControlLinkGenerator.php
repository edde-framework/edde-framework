<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Common\Url\Url;

	class ControlLinkGenerator extends AbstractLinkGenerator {
		use LazyHostUrlTrait;
		use LazyRequestUrlTrait;

		public function link($generate, ...$parameterList) {
			list($generate, $parameterList) = $this->list($generate, $parameterList);
			if (is_array($generate) === false || count($generate) !== 2) {
				return null;
			}
			list($control, $action) = $generate;
			if (class_exists($control = is_object($control) ? get_class($control) : $control) === false) {
				return null;
			}
			$parameterList['action'] = $control . '.' . $action;
			return Url::create($this->hostUrl->getAbsoluteUrl())
				->setQuery(array_merge($this->requestUrl->getQuery(), $parameterList))
				->getAbsoluteUrl();
		}
	}
