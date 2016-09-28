<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\ISession;
	use Edde\Api\Session\LazySessionManagerTrait;
	use Edde\Common\Deffered\Event\OnPrepareEvent;

	/**
	 * Helper trait for simple work with session section.
	 */
	trait SessionTrait {
		use LazySessionManagerTrait;
		/**
		 * @var ISession
		 */
		protected $session;

		/**
		 * ultimately long name to prevent clashes; shoud be called automagically
		 *
		 * @param OnPrepareEvent $onPrepareEvent
		 */
		public function eventSessionTraitOnPrepareEvent(OnPrepareEvent $onPrepareEvent) {
			$this->lazy('session', function () {
				return $this->sessionManager->getSession(static::class);
			});
		}
	}
