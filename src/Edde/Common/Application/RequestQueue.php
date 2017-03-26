<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IRequestQueue;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	class RequestQueue extends Object implements IRequestQueue {
		use ConfigurableTrait;
		/**
		 * @var IRequest[]
		 */
		protected $requestList = [];

		/**
		 * @inheritdoc
		 */
		public function queue(IRequest $request): IRequestQueue {
			$this->requestList[] = $request;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isEmpty(): bool {
			return empty($this->requestList);
		}

		/**
		 * @inheritdoc
		 */
		public function getIterator() {
			return new \ArrayIterator($this->requestList);
		}
	}
