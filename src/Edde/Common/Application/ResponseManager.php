<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponse;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Application\LazyRequestTrait;
	use Edde\Api\Converter\LazyConverterManagerTrait;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	class ResponseManager extends Object implements IResponseManager {
		use LazyConverterManagerTrait;
		use LazyRequestTrait;
		use ConfigurableTrait;
		/**
		 * @var IResponse
		 */
		protected $response;

		public function response(IResponse $response = null): IResponseManager {
			$this->response = $response;
			return $this;
		}

		public function execute() {
			if ($this->response === null) {
				return;
			}
			$this->converterManager->setup();
			$this->converterManager->content($this->response, $this->response->getTargetList())
				->convert();
		}
	}
