<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Request;

	use Edde\Api\Protocol\Request\IRequest;

	class Request extends Message implements IRequest {
		public function __construct(string $request, string $id = null) {
			parent::__construct($request, 'request', $id);
		}

		public function packet(): \stdClass {
			$packet = parent::packet();
			$packet->request = $this->getRequest();
			return $packet;
		}
	}
