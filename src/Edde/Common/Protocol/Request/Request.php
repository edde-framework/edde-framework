<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Request;

	use Edde\Api\Protocol\Request\IRequest;
	use Edde\Common\Protocol\AbstractElement;

	class Request extends AbstractElement implements IRequest {
		/**
		 * @var string
		 */
		protected $request;

		public function __construct(string $request = null, string $id = null) {
			parent::__construct('request', $id);
			$this->request = $request;
		}

		/**
		 * @inheritdoc
		 */
		public function setRequest(string $request): IRequest {
			$this->request = $request;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getRequest(): string {
			return $this->request;
		}

		/**
		 * @inheritdoc
		 */
		public function packet(): \stdClass {
			$packet = parent::packet();
			$packet->request = $this->getRequest();
			return $packet;
		}

		public function __clone() {
			parent::__clone();
			$this->request = null;
		}
	}
