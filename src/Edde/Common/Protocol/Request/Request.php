<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol\Request;

	use Edde\Common\Protocol\Element;

	class Request extends Element {
		public function __construct(string $request = null, string $id = null) {
			parent::__construct('request', $id);
			$this->setAttribute('request', $request);
		}

		/**
		 * @inheritdoc
		 */
		public function getRequest(): string {
			return (string)$this->getAttribute('request');
		}
	}
