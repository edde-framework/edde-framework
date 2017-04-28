<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Request;

	use Edde\Api\Protocol\Request\IRequest;

	class Request extends Message implements IRequest {
		public function __construct(string $request) {
			parent::__construct($request, 'request');
		}
	}
