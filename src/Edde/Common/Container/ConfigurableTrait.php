<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\IConfigHandler;

	trait ConfigurableTrait {
		/**
		 * @var bool
		 */
		private $tInit = false;
		/**
		 * @var bool
		 */
		private $tConfig = false;
		/**
		 * @var IConfigHandler[]
		 */
		private $tConfigHandlerList = [];

		/**
		 * @inheritdoc
		 */
		public function registerConfigHandlerList(array $configHandlerList) {
			$this->tConfigHandlerList = $configHandlerList;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function init() {
			if ($this->tInit) {
				return;
			}
			$this->tInit = true;
			$this->prepare();
		}

		protected function prepare() {
		}

		/**
		 * @inheritdoc
		 */
		public function isInitialized(): bool {
			return $this->tInit;
		}

		/**
		 * @inheritdoc
		 */
		public function config() {
			if ($this->tConfig) {
				return $this;
			}
			$this->init();
			$this->tConfig = true;
			foreacH ($this->tConfigHandlerList as $configHandler) {
				$configHandler->config($this);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isConfigured(): bool {
			return $this->tConfig;
		}
	}
