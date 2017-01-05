<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IRequestUrl;
	use Edde\Api\Http\IRequestUrlFactory;
	use Edde\Common\Object;

	class RequestUrlFactory extends Object implements IRequestUrlFactory {
		public function create(): IRequestUrl {
			return RequestUrl::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		}
	}
