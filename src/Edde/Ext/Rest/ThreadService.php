<?php
	declare(strict_types=1);

	namespace Edde\Ext\Rest;

	use Edde\Api\Thread\LazyThreadManagerTrait;
	use Edde\Api\Url\IUrl;
	use Edde\Common\Protocol\Request\Response;
	use Edde\Common\Rest\AbstractService;

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
		 */
		public function actionHead() {
			$this->threadManager->pool();
			return new Response();
		}
	}
