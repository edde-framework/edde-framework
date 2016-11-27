<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event\Handler;

	class SelfHandler extends ReflectionHandler {
		public function __construct(string $scope = null) {
			parent::__construct($this, $scope);
		}
	}
