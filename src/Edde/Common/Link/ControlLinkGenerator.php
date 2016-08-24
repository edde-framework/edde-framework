<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Link\IHostUrl;
	use Edde\Api\Link\ILinkGenerator;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Url\Url;

	class ControlLinkGenerator extends AbstractObject implements ILinkGenerator {
		use LazyInjectTrait;
		/**
		 * @var IHostUrl
		 */
		protected $hostUrl;

		public function lazyHostUrl(IHostUrl $hostUrl) {
			$this->hostUrl = $hostUrl;
		}

		public function generate($generate, ...$parameterList) {
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
