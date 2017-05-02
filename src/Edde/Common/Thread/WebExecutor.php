<?php
	declare(strict_types=1);

	namespace Edde\Common\Thread;

	use Edde\Api\Http\Client\LazyHttpClientTrait;
	use Edde\Api\Thread\IExecutor;
	use Edde\Api\Url\IUrl;
	use Edde\Api\Url\UrlException;
	use Edde\Common\Url\Url;

	class WebExecutor extends AbstractExecutor {
		use LazyHttpClientTrait;
		/**
		 * @var IUrl
		 */
		protected $url;

		/**
		 * @param string|IUrl $url
		 *
		 * @return IExecutor
		 * @throws UrlException
		 */
		public function setUrl($url): IExecutor {
			$this->url = Url::create($url);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function execute(): IExecutor {
			$this->httpClient->touch($this->url);
			return $this;
		}
	}
