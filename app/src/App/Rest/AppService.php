<?php
	declare(strict_types = 1);

	namespace App\Rest;

	use Edde\Api\Url\IUrl;
	use Edde\Common\Html\TemplateTrait;
	use Edde\Common\Rest\AbstractService;

	class AppService extends AbstractService {
		use TemplateTrait;

		public function match(IUrl $url): bool {
			return strpos($url->getPath(), '/rest/v1/app') !== false;
		}

		public function restGet() {
			$this->response(static::class . ' rest api!', 'text/plain');
		}
	}
