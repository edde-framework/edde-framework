<?php
	declare(strict_types = 1);

	namespace Edde\Common\Resource;

	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\ResourceException;
	use Edde\Api\Url\IUrl;
	use Edde\Common\AbstractObject;
	use Edde\Common\File\FileUtils;

	class Resource extends AbstractObject implements IResource {
		/**
		 * @var IUrl
		 */
		protected $url;
		/**
		 * @var string
		 */
		protected $base;
		/**
		 * friendly name of this resource
		 *
		 * @var string
		 */
		protected $name;
		/**
		 * @var string
		 */
		protected $mime;

		/**
		 * @param IUrl $url
		 * @param string|null $base
		 * @param string|null $name
		 * @param string|null $mime
		 */
		public function __construct(IUrl $url, $base = null, $name = null, $mime = null) {
			$this->url = $url;
			$this->base = $base;
			$this->name = $name;
			$this->mime = $mime;
		}

		public function getUrl() {
			return $this->url;
		}

		public function getRelativePath($base = null) {
			if ($this->base === null && $base === null) {
				throw new ResourceException(sprintf('Cannot compute relative path of a resource [%s]; there is not base path.', $this->url->getPath()));
			}
			if (strpos($path = $this->url->getPath(), $base = $base ?: $this->base) === false) {
				throw new ResourceException(sprintf('Cannot compute relative path of resource; given base path [%s] is not subset of the current path [%s].', $base, $path));
			}
			return str_replace($base, null, $path);
		}

		public function getBase() {
			return $this->base;
		}

		public function getName() {
			return $this->name;
		}

		public function getMime() {
			if ($this->mime === null) {
				$this->mime = FileUtils::mime($this->url->getAbsoluteUrl());
			}
			return $this->mime;
		}

		public function isAvailable() {
			return file_exists($url = $this->url->getAbsoluteUrl()) && is_readable($url);
		}

		public function get() {
			return file_get_contents($this->url->getAbsoluteUrl());
		}

		public function getIterator() {
			throw new ResourceException(sprintf('Iterator is not supported on raw [%s].', static::class));
		}
	}
