<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\IEvent;
	use Edde\Common\Object;

	/**
	 * Common stuff for event implementation.
	 */
	abstract class AbstractEvent extends Object implements IEvent {
	}
