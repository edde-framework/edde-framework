<?php
	declare(strict_types = 1);

	use Edde\Common\Event\AbstractEvent;

	class SomeEvent extends AbstractEvent {
		public $flag = false;
	}
