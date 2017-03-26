<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\LazyRequestQueueTrait;
	use Edde\Api\Application\LazyResponseManagerTrait;
	use Edde\Common\Object;

	/**
	 * Common implementation for all applications.
	 */
	abstract class AbstractApplication extends Object implements IApplication {
		use LazyRequestQueueTrait;
		use LazyResponseManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function run() {
			$this->requestQueue->setup();
			if ($this->requestQueue->isEmpty()) {
				throw new EmptyRequestQueueException('The Application request queue is empty, oops!');
			}
			$response = null;
			foreach ($this->requestQueue as $request) {
				$response = $this->execute($request);
			}
			$this->responseManager->response($response);
			$this->responseManager->execute();
		}
	}
