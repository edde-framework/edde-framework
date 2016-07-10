<?php
	namespace Edde\Common\Usable;

	/**
	 * Use this trait where is not possible to use inheritance!
	 */
	trait UsableTrait {
		/**
		 * @var bool
		 */
		protected $used = false;
		/**
		 * @var callable[]
		 */
		protected $onSetupList = [];
		/**
		 * @var callable[]
		 */
		protected $onUseList = [];

		public function onSetup(callable $callback) {
			if ($this->isUsed()) {
				throw new UsableException(sprintf('Cannot add onSetup callback to already used usable [%s].', static::class));
			}
			$this->onSetupList[] = $callback;
			return $this;
		}

		public function isUsed() {
			return $this->used;
		}

		public function onUse(callable $callback) {
			if ($this->isUsed()) {
				throw new UsableException(sprintf('Cannot add onUse callback to already used usable [%s].', static::class));
			}
			$this->onUseList[] = $callback;
			return $this;
		}

		public function onLoaded(callable $callback) {
			if ($this->isUsed()) {
				call_user_func($callback, $this);
				return $this;
			}
			$this->onUseList[] = $callback;
			return $this;
		}

		public function usse() {
			if ($this->used === false) {
				$this->used = true;
				foreach ($this->onSetupList as $callback) {
					call_user_func($callback, $this);
				}
				$this->prepare();
				foreach ($this->onUseList as $callback) {
					call_user_func($callback, $this);
				}
			}
			return $this;
		}

		/**
		 * prepare this class for the first usage
		 *
		 * @return mixed
		 */
		abstract protected function prepare();
	}
