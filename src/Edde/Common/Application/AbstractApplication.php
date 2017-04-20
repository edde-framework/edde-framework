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
			/**
			 * Because there is no way how to explicitly setup request queue, this enabled user to register some
			 * configurators which are executed here, thus request queue is prepared here.
			 *
			 * In this part could be executed for example router (service) to get proper request to the application.
			 */
			$this->requestQueue->setup();
			/**
			 * Application cannot create it's own response because it doesn't know in which context it's
			 * running (http/cli); that means general failure, because without request application do no response
			 * which is considered as a bad state.
			 *
			 * If the application is running for example as a thread, question to request queue must be done before
			 * application run (or use different application implementation).
			 */
			if ($this->requestQueue->isEmpty()) {
				throw new EmptyRequestQueueException('The Application request queue is empty, oops!');
			}
			$response = null;
			/**
			 * So an application can be executed with more requests, but these requests should not modify
			 * and application or "echo" something; they have to return only response which would be processed
			 * later in response manager.
			 *
			 * This means that response must be as lightweight as possible; response is usually bound to some
			 * particular converter which is able to handle output conversion.
			 */
			foreach ($this->requestQueue as $request) {
				$response = $this->execute($request);
			}
			/**
			 * this would respect requests which explicitly set some request
			 */
			if ($this->responseManager->hasResponse() === false) {
				$this->responseManager->response($response);
			}
			/**
			 * Here is basically main login of an application; response manager is responsible
			 * for getting input type and execute conversion to the output type.
			 *
			 * This is usually done by getting http accept header and setting it as an output type
			 * and then executing converter manager to get proper output.
			 */
			$this->responseManager->execute();
		}
	}
