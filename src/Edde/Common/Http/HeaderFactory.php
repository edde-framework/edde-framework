<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IHeaderFactory;
	use Edde\Api\Http\IHeaderList;
	use Edde\Common\Object;

	class HeaderFactory extends Object implements IHeaderFactory {
		public function create(): IHeaderList {
			$headers = [];
			$mysticList = [
				'CONTENT_TYPE' => 'Content-Type',
				'CONTENT_LENGTH' => 'Content-Length',
				'CONTENT_MD5' => 'Content-Md5',
			];
			/** @noinspection ForeachSourceInspection */
			foreach ($_SERVER as $key => $value) {
				if (empty($value)) {
					continue;
				}
				if (strpos($key, 'HTTP_') === 0) {
					$key = substr($key, 5);
					if (isset($mysticList[$key]) === false || isset($_SERVER[$key]) === false) {
						$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
						$headers[$key] = $value;
					}
				} else if (isset($mysticList[$key])) {
					$headers[$mysticList[$key]] = $value;
				}
			}
			if (isset($headers['Authorization']) === false) {
				if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
					$headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
				} else if (isset($_SERVER['PHP_AUTH_USER'])) {
					$password = $_SERVER['PHP_AUTH_PW'] ?? '';
					$headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $password);
				} else if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
					$headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
				}
			}
			return (new HeaderList())->put($headers);
		}
	}
