<?php
	declare(strict_types = 1);

	namespace Edde\Common\Session;

	use Edde\Api\Session\ISession;
	use Edde\Api\Session\LazySessionManagerTrait;
	use Edde\Common\Deffered\Event\OnDefferedEvent;

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
		 * @param $onDefferedEvent $onDefferedEvent
		 */
		public function eventSessionTraitOnPrepareEvent(OnDefferedEvent $onDefferedEvent) {
			$this->lazy('session', function () {
				return $this->sessionManager->getSession(static::class);
			});
		}
	}
