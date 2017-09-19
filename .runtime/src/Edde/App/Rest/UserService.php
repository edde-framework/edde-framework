<?php
	declare(strict_types=1);

	namespace Edde\App\Rest;

	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;

	class UserService extends AbstractService {
		use Edde\Api\Http\Inject\LazyHostUrlTrait;

		public function match(IUrl $url): bool {
			return $url->match('~^/api/v1/user$~') !== null;
		}

		public function link($generate, array $parameterList = []) {
			return parent::link('/api/v1/user', $parameterList);
		}
	}
