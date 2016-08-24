<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Http;

	use Edde\Api\File\IFile;
	use Edde\Common\AbstractObject;

	class Handle extends AbstractObject {
		/**
		 * @var resource
		 */
		protected $curl;
		protected $headerList = [];
		protected $contentType;
		protected $contentTypeHandlerList = [];
		protected $url;
		protected $content;
		/**
		 * @var IFile
		 */
		protected $file;

		/**
		 * @param resource $curl
		 * @param string $url
		 */
		public function __construct($curl, string $url) {
			$this->curl = $curl;
			$this->url = $url;
		}

		/**
		 * when given content type *exactly* matches, callback is executed:
		 *
		 * function(Handle $handle) {
		 *      ...
		 *      // e.g. $handle->getContent()
		 *      ...
		 * }
		 *
		 * @param string $contentType
		 * @param callable $contentTypeHandler
		 *
		 * @return $this
		 */
		public function registerContentTypeHandler($contentType, callable $contentTypeHandler) {
			$this->contentTypeHandlerList[$contentType] = $contentTypeHandler;
			return $this;
		}

		public function getContents() {
			$this->exec();
			return $this->content === true ? $this->file->get() : $this->content;
		}

		public function exec() {
			if ($this->curl === null) {
				return $this;
			}
			curl_setopt_array($this->curl, [
				CURLOPT_HEADERFUNCTION => function ($curl, $header) {
					$length = strlen($header);
					if (($text = trim($header)) !== '' && strpos($header, ':') !== false) {
						list($header, $content) = explode(':', $header, 2);
						$this->headerList[strtolower($header)] = trim($content);
					}
					return $length;
				},
			]);
			$this->content = curl_exec($this->curl);
			if ($this->content === false) {
				$error = curl_error($this->curl);
				$errorCode = curl_errno($this->curl);
				curl_close($this->curl);
				$this->curl = null;
				throw new ClientException(sprintf('%s: %s', $this->url, $error), $errorCode);
			}
			$this->contentType = substr($contentType = curl_getinfo($this->curl, CURLINFO_CONTENT_TYPE), 0, ($index = strpos($contentType, ';')) === false ? strlen($contentType) : $index);
			curl_close($this->curl);
			$this->curl = null;
			if (isset($this->contentTypeHandlerList[$this->contentType])) {
				$this->content = $this->contentTypeHandlerList[$this->contentType]($this);
			}
			return $this;
		}

		/**
		 * @return IFile
		 */
		public function getFile() {
			return $this->file;
		}

		public function getContentType() {
			$this->exec();
			return $this->contentType;
		}

		public function getHeaderList() {
			$this->exec();
			return $this->headerList;
		}

		public function getHeader($name, $default = null) {
			return isset($this->headerList[$name = strtolower($name)]) ? $this->headerList[$name] : $default;
		}

		/**
		 * execute request and saves output into file
		 *
		 * @param IFile $file
		 *
		 * @return IFile
		 */
		public function save(IFile $file) {
			$this->file = $file;
			$this->file->openForWrite();
			curl_setopt_array($this->curl, [
				CURLOPT_RETURNTRANSFER => false,
				CURLOPT_FILE => $this->file->getHandle(),
			]);
			$this->exec();
			$this->file->close();
			return $this->file;
		}
	}
