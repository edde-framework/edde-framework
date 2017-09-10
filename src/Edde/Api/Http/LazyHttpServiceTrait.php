<?php
	declare(strict_types=1);

	namespace Edde\Api\Http;

	trait LazyHttpServiceTrait {
		/**
		 * @var IHttpService
		 */
		protected $httpService;

		/**
		 * @param IHttpService $httpService
		 */
		public function lazyHttpService(IHttpService $httpService) {
			$this->httpService = $httpService;
		}
	}
