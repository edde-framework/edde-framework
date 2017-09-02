<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Url\IUrl;
	use Edde\Common\Url\Url;

	class RequestUrl extends Url {
		/**
		 * @var IUrl
		 */
		static protected $url;

		static public function factory(): IUrl {
			return self::$url ?: self::$url = self::create((isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		}
	}
