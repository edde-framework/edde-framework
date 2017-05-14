<?php
	declare(strict_types=1);

	namespace Edde\Api\Protocol\Event;

	use Edde\Api\Node\INode;
	use Edde\Api\Protocol\IProtocolHandler;

	interface IEventBus extends IProtocolHandler {
		/**
		 * @param IListener $listener
		 *
		 * @return IEventBus
		 */
		public function register(IListener $listener): IEventBus;

		/**
		 * register listener for the given event
		 *
		 * @param string   $event
		 * @param callable $callback
		 *
		 * @return IEventBus
		 */
		public function listen(string $event, callable $callback): IEventBus;

		/**
		 * immediately emmit the given event
		 *
		 * @param INode $node
		 *
		 * @return IEventBus
		 */
		public function emit(INode $node): IEventBus;
	}
