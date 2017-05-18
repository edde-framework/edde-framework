<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Thread\LazyThreadManagerTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;
	use Edde\Ext\Application\JsonResponse;

	class ThreadService extends AbstractService {
		use LazyThreadManagerTrait;

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

		/**
		 * head because client should not expect "output" except of headers; in general this method should not return nothing at all because
		 * in general is is a long running task dequeing all current jobs
		 *
		 * @return JsonResponse
		 */
		public function restHead() {
			$this->threadManager->pool();
			return new JsonResponse(true);
		}
	}
