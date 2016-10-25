<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Http\LazyRequestUrlTrait;
	use Edde\Api\Link\LinkException;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;

	class ControlLinkGenerator extends AbstractLinkGenerator {
		use LazyRequestTrait;
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

			$contextRegexp = 'context=(?<contextHandle>[a-zA-Z0-9$-]+)';
			$handleRegexp = 'handle=(?<handleHandle>[a-zA-Z0-9-]+)';
			if (($match = StringUtils::match($action, '~^' . $contextRegexp . '$~')) !== null) {
				$parameterList['context'] = $parameterList['handle'] = $this->filter($match['contextHandle'], $control);
			} else if (($match = StringUtils::match($action, '~^' . $contextRegexp . ',' . $handleRegexp . '$~')) !== null) {
				$parameterList['context'] = $this->filter($match['contextHandle'], $control);
				$parameterList['handle'] = $this->filter($match['handleHandle'], $control);
			} else if (($match = StringUtils::match($action, '~^' . $handleRegexp . '$~')) !== null) {
				$parameterList['handle'] = $this->filter($match['handleHandle'], $control);
			} else {
				return null;
			}
			return Url::create()
				->setQuery(array_merge($this->request->getParameterList(), $parameterList))
				->getAbsoluteUrl();
		}

		protected function filter(string $input, string $control): string {
			switch ($input) {
				case '$':
					$query = $this->requestUrl->getQuery();
					if (isset($query['context']) === false) {
						throw new LinkException('Context is required for contextual link generation support.');
					}
					return $query['context'];
			}
			return $control . '.' . $input;
		}
	}
