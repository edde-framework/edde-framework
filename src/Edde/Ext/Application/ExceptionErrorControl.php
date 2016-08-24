<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Application;

	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Http\IHttpRequest;
	use Edde\Api\Router\RouterException;
	use Edde\Ext\Html\EddeViewControl;

	/**
	 * Only rethrows exception.
	 */
	class ExceptionErrorControl extends EddeViewControl implements IErrorControl {
		/**
		 * @var IHttpRequest
		 */
		protected $httpRequest;

		public function lazyHttpRequest(IHttpRequest $httpRequest) {
			$this->httpRequest = $httpRequest;
		}

		public function exception(\Exception $e) {
			if ($this->httpRequest->isAjax()) {
				throw $e;
			}
			try {
				throw $e;
			} catch (RouterException $e) {
				$this->template(__DIR__ . '/template/404.xml');
				$this->send();
			} catch (\Exception $e) {
				$this->template(__DIR__ . '/template/500.xml');
				$this->send();
			}
		}
	}
