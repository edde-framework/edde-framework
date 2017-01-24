<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IHttpRequest;
	use Edde\Common\Converter\Content;

	class HttpRequest extends Request implements IHttpRequest {
		/**
		 * @var IHttpRequest
		 */
		static protected $httpReqeust;

		static public function createHttpRequest(): IHttpRequest {
			self::$httpReqeust ?: self::$httpReqeust = new HttpRequest(RequestUrl::createRequestUrl(), $headerList = HeaderList::createHeaderList(), CookieList::createCookieList());
			$input = fopen('php://input', 'r');
			if (empty($_POST) === false) {
				$content = new Content($_POST, 'post');
			} else if (fgetc($input) !== false) {
				$contentType = $headerList->getContentType();
				$contentType->init();
				$content = new Content('php://input', 'stream+' . $contentType->getMime());
			}
			fclose($input);
			isset($content) ? self::$httpReqeust->setContent($content) : null;
			return self::$httpReqeust;
		}
	}
