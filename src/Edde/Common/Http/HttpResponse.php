<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IHttpResponse;

	class HttpResponse extends Response implements IHttpResponse {
		static public function createHttpResponse(): IHttpResponse {
			return new self(200, new HeaderList(), new CookieList());
		}
	}
