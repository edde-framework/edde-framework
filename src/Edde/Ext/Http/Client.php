<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Http;

	use Edde\Common\AbstractObject;

	class Client extends AbstractObject {
		/**
		 * @var resource
		 */
		protected $curl;
		/**
		 * @var string
		 */
		protected $url;
		/**
		 * @var array
		 */
		protected $headerList;
		/**
		 * @var string
		 */
		protected $encoding;
		/**
		 * @var string
		 */
		protected $userAgent;
		protected $sslCheck;
		protected $timeOut = 10;

		public function __construct($url = null) {
			$this->url = $url;
			$this->headerList = [];
			$this->encoding = 'utf-8';
			$this->sslCheck = true;
			$this->timeOut = 10;
		}

		public function getUrl() {
			return $this->url;
		}

		/**
		 * @param string $url
		 *
		 * @return $this
		 */
		public function setUrl($url) {
			$this->url = $url;
			return $this;
		}

		public function setEncoding($encoding) {
			$this->encoding = $encoding;
			return $this;
		}

		/**
		 * @param string $userAgent
		 *
		 * @return $this
		 */
		public function setUserAgent($userAgent) {
			$this->userAgent = $userAgent;
			return $this;
		}

		public function disableSslCheck() {
			$this->sslCheck = false;
			return $this;
		}

		/**
		 * @param int $timeOut
		 *
		 * @return $this
		 */
		public function setTimeOut($timeOut) {
			$this->timeOut = (int)$timeOut;
			return $this;
		}

		public function setAuth($user, $password) {
			$this->addHeader('HTTP_AUTH_LOGIN', $user);
			$this->addHeader('HTTP_AUTH_PASSWD', $password);
			return $this;
		}

		public function addHeader($header, $value) {
			$this->headerList[$header] = "$header: $value";
			return $this;
		}

		public function setContentType($contentType) {
			$this->addHeader('Content-Type', $contentType);
			return $this;
		}

		/**
		 * @return Handle
		 */
		public function get() {
			return $this->setup();
		}

		/**
		 * @return Handle
		 */
		private function setup() {
			$this->curl = curl_init($this->url);
			$options = [
				CURLOPT_SSL_VERIFYPEER => (bool)$this->sslCheck,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_FAILONERROR => true,
				CURLOPT_FORBID_REUSE => true,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => $this->encoding,
				CURLOPT_HTTPHEADER => $this->headerList,
				CURLOPT_URL => $this->url,
				CURLOPT_CONNECTTIMEOUT => 5,
				CURLOPT_TIMEOUT => $this->timeOut,
			];
			if ($this->userAgent !== null) {
				$options[CURLOPT_USERAGENT] = $this->userAgent;
			}
			curl_setopt_array($this->curl, $options);
			return new Handle($this->curl, $this->url);
		}

		/**
		 * @param array|null $post
		 *
		 * @return Handle
		 */
		public function post($post = null) {
			$handle = $this->setup();
			curl_setopt($this->curl, CURLOPT_POST, true);
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
			return $handle;
		}

		public function put($post = null) {
			$handle = $this->setup();
			curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
			return $handle;
		}

		public function delete($post = null) {
			$handle = $this->setup();
			curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
			return $handle;
		}
	}
