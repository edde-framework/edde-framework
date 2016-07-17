<?php
	namespace Edde\Common\Url;

	use Edde\Api\Url\IUrl;
	use Edde\Api\Url\UrlException;
	use Edde\Common\AbstractObject;

	class Url extends AbstractObject implements IUrl {
		/**
		 * @var string
		 */
		private $scheme;
		/**
		 * @var string
		 */
		private $user;
		/**
		 * @var string
		 */
		private $password;
		/**
		 * @var string
		 */
		private $host;
		/**
		 * @var int
		 */
		private $port;
		/**
		 * @var string
		 */
		private $path;
		/**
		 * @var array
		 */
		private $query = [];
		/**
		 * @var string
		 */
		private $fragment;

		static public function create($url = null) {
			$self = new self();
			if ($url !== null) {
				$self->build($url);
			}
			return $self;
		}

		public function build($url) {
			if (($parsed = parse_url($url)) === false) {
				throw new UrlException(sprintf('Malformed URL [%s].', $url));
			}
			if (isset($parsed['query'])) {
				parse_str($parsed['query'], $parsed['query']);
			}
			static $copy = [
				'scheme' => 'setScheme',
				'user' => 'setUser',
				'pass' => 'setPassword',
				'host' => 'setHost',
				'port' => 'setPort',
				'path' => 'setPath',
				'query' => 'setQuery',
				'fragment' => 'setFragment',
			];
			foreach ($copy as $item => $func) {
				if (isset($parsed[$item])) {
					$this->{$func}($parsed[$item]);
				}
			}
			return $this;
		}

		public function getResourceName() {
			$pathList = $this->getPathList();
			return end($pathList);
		}

		public function getPathList() {
			return explode('/', ltrim($this->path, '/'));
		}

		public function getExtension() {
			$path = $this->getPath();
			$subpath = substr($path, strrpos($path, '/'));
			if (($index = strrpos($subpath, '.')) === false) {
				return null;
			}
			return substr($subpath, $index + 1);
		}

		public function getPath() {
			return $this->path;
		}

		public function setPath($path) {
			$this->path = $path;
			return $this;
		}

		public function __toString() {
			return $this->getAbsoluteUrl();
		}

		public function getAbsoluteUrl() {
			$scheme = $this->getScheme();
			$url = null;
			if ($scheme !== null) {
				$url = $scheme . '://';
			}
			if (($user = $this->getUser()) !== null) {
				$url .= $user;
				if (($password = $this->getPassword()) !== null) {
					$url .= ':' . $password;
				}
				$url .= '@';
			}
			$url .= ($host = $this->getHost());
			if ($host !== null && ($port = $this->getPort()) !== null) {
				$url .= ':' . $port;
			}
			$url .= '/' . ltrim($this->getPath(), '/');
			$query = $this->getQuery();
			if (empty($query) === false) {
				$url .= '?' . http_build_query($this->getQuery());
			}
			if (($fragment = $this->getFragment()) !== null) {
				$url .= '#' . $fragment;
			}
			return $url;
		}

		public function getScheme() {
			return $this->scheme;
		}

		public function setScheme($scheme) {
			$this->scheme = $scheme;
			return $this;
		}

		public function getUser() {
			return $this->user;
		}

		public function setUser($user) {
			$this->user = $user;
			return $this;
		}

		public function getPassword() {
			return $this->password;
		}

		public function setPassword($password) {
			$this->password = $password;
			return $this;
		}

		public function getHost() {
			return $this->host;
		}

		public function setHost($host) {
			$this->host = $host;
			return $this;
		}

		public function getPort() {
			return $this->port;
		}

		public function setPort($port) {
			$this->port = $port;
			return $this;
		}

		public function getQuery() {
			return $this->query;
		}

		public function setQuery(array $query) {
			$this->query = $query;
			return $this;
		}

		public function getFragment() {
			return $this->fragment;
		}

		public function setFragment($fragment) {
			$this->fragment = $fragment;
			return $this;
		}

		public function getParameter($name, $default = null) {
			if (isset($this->query[$name]) === false) {
				return $default;
			}
			return $this->query[$name];
		}
	}
