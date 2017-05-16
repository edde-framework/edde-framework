<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Thread\LazyThreadCountTrait;
	use Edde\Api\Thread\LazyThreadManagerTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Rest\AbstractService;
	use Edde\Ext\Application\JsonResponse;

	class ThreadService extends AbstractService {
		use LazyThreadManagerTrait;
		use LazyThreadCountTrait;

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
			$success = false;
			try {
				if ($this->threadCount->canExecute()) {
					$this->threadCount->increase();
					$this->threadManager->dequeue();
					$success = true;
					/**
					 * if there are still some tasks in queue, do thread execution againl; this thread should end and one one should be executed
					 */
					if ($this->threadManager->hasQueue()) {
						$this->threadManager->execute();
					}
				}
				return new JsonResponse($success);
			} finally {
				$this->threadCount->decrease();
			}
		}
	}
