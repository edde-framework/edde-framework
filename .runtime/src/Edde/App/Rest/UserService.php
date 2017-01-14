<?php
	declare(strict_types=1);

	namespace Edde\App\Rest;

	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;

	class UserService extends AbstractService {
		use LazyHostUrlTrait;

		public function link($generate, ...$parameterList) {
			$url = clone $this->hostUrl;
			$url->setPath('/v1/user');
			$url->setQuery($parameterList);
			return $url;
		}

		public function match(IUrl $url): bool {
			return $url->match('~^/v1/user$~') !== null;
		}
	}
