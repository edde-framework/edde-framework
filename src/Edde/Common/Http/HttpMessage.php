<?php
	declare(strict_types = 1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IHeaderList;
	use Edde\Api\Http\IHttpMessage;
	use Edde\Common\Deffered\AbstractDeffered;

	class HttpMessage extends AbstractDeffered implements IHttpMessage {
		/**
		 * @var string
		 */
		protected $message;
		/**
		 * @var string
		 */
		protected $headers;
		/**
		 * @var IHeaderList
		 */
		protected $headerList;

		/**
		 * HttpMessage constructor.
		 *
		 * @param string $message
		 * @param string $headers
		 */
		public function __construct($message, $headers) {
			$this->message = $message;
			$this->headers = $headers;
		}

		public function getHeaderList(): IHeaderList {
			$this->use();
			return $this->headerList;
		}

		/**
		 * return content type from input headers
		 *
		 * @return string|null
		 */
		public function getContentType(): string {
			$this->use();
			return $this->headerList->getContentType('');
		}

		protected function prepare() {
			$this->headerList = new HeaderList();
			$this->headerList->put(HttpUtils::headerList($this->headers));
		}
	}
