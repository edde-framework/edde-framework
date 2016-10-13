<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Common\Url\Url;

	class ControlLinkGenerator extends AbstractLinkGenerator {
		use LazyHostUrlTrait;

		public function link($generate, ...$parameterList) {
			list($generate, $parameterList) = $this->list($generate, $parameterList);
			if (is_array($generate) === false || count($generate) !== 2) {
				return null;
			}
			list($control, $action) = $generate;
			$control = is_object($control) ? get_class($control) : $control;
			if (class_exists($control) === false) {
				return null;
			}
			$url = Url::create($this->hostUrl->getAbsoluteUrl());
			$url->setQuery(array_merge($url->getQuery(), [
				'control' => $control,
				'action' => $action,
			], $parameterList));
			return $url->getAbsoluteUrl();
		}
	}
