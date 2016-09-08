<?php
	declare(strict_types = 1);

	namespace TestRouter;

	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;

	class TestService extends AbstractService {
		public function match(IUrl $url): bool {
			return strpos((string)$url, '/api/test-service') !== false;
		}

		public function restGet() {
		}

		public function restDelete() {
		}
	}
