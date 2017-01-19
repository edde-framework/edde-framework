<?php
	declare(strict_types=1);

	namespace Edde\App\Rest;

	use Edde\Api\Http\LazyHostUrlTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;

	class UserService extends AbstractService {
		use LazyHostUrlTrait;

		public function link($generate, ...$parameterList) {
			return parent::link('/api/v1/user', ...$parameterList);
		}

		public function match(IUrl $url): bool {
			return $url->match('~^/api/v1/user$~') !== null;
		}
	}
