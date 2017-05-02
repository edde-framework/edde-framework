<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Http\Client\LazyHttpClientTrait;
	use Edde\Api\Link\LazyLinkFactoryTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;
	use Edde\Ext\Application\JsonResponse;

	class ProtocolService extends AbstractService {
		use LazyHttpClientTrait;
		use LazyLinkFactoryTrait;

		/**
		 * @inheritdoc
		 */
		public function match(IUrl $url): bool {
			return $url->match('~^/api/v1/protocol$~') !== null;
		}

		public function restGet($scope = null, array $tags = null) {
			$this->linkFactory->setup();
			$this->httpClient->touch($this->linkFactory->link(ThreadService::class, null));
			return new JsonResponse(static::class);
		}
	}
