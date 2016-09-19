<?php
	declare(strict_types = 1);

	namespace Edde\Common\Event;

	use Edde\Api\Event\EventException;
	use Edde\Common\AbstractObject;
	use Edde\Common\Event\Handler\CallableHandler;
	use Edde\Common\Event\Handler\ReflectionHandler;

	class HandlerFactory extends AbstractObject {
		static public function handler($handler) {
			if (is_callable($handler)) {
				return new CallableHandler($handler);
			} else if (is_object($handler)) {
				return new ReflectionHandler($handler);
			}
			throw new EventException(sprintf('Cannot create handler from type [%s].', gettype($handler)));
		}
	}