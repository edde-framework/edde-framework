<?php
	declare(strict_types = 1);

	namespace App\Rest;

	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;

	class AppService extends AbstractService {
		public function match(IUrl $url): bool {
			return strpos($url->getPath(), '/rest/v1/app') !== false;
		}
	}
