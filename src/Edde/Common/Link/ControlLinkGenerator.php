<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Http\IHostUrl;
	use Edde\Common\Url\Url;

	class ControlLinkGenerator extends AbstractLinkGenerator {
		/**
		 * @var IHostUrl
		 */
		protected $hostUrl;

		public function lazyHostUrl(IHostUrl $hostUrl) {
			$this->hostUrl = $hostUrl;
		}

		public function link($generate, ...$parameterList) {
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
			]));
			return $url->getAbsoluteUrl();
		}
	}
