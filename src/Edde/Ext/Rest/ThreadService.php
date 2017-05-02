<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Log\LazyLogServiceTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;
	use Edde\Ext\Application\JsonResponse;

	class ThreadService extends AbstractService {
		use LazyLogServiceTrait;

		/**
		 * @inheritdoc
		 */
		public function match(IUrl $url): bool {
			return $url->match('~^/api/v1/thread~') !== null;
		}

		/**
		 * @inheritdoc
		 */
		public function link($generate, ...$parameterList) {
			return parent::link('/api/v1/thread', ...$parameterList);
		}

		public function restHead() {
			return new JsonResponse(static::class);
		}
	}
