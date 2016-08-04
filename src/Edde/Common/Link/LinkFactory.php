<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Link\LinkException;
	use Edde\Common\AbstractObject;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Url\Url;

	class LinkFactory extends AbstractObject implements ILinkFactory {
		public function linkTo($class, $method, array $parameterList = []) {
			$className = [];
			foreach (explode('\\', is_object($class) ? get_class($class) : $class) as $part) {
				$className[] = StringUtils::recamel($part);
			}
			return Url::create('/' . implode('/', $className) . '/' . StringUtils::recamel($method))
				->setQuery($parameterList);
		}

		public function generate($generate) {
			$uri = null;
			if (is_string($generate)) {
				return $this->link($generate);
			}
			if ($uri === null) {
				throw new LinkException(sprintf('Unknown input argument [%s] for the link generator [%s].', gettype($generate), static::class));
			}
			return $uri;
		}

		public function link($link) {
		}
	}
