<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\IConfigHandler;

	trait ConfigurableTrait {
		/**
		 * @var IConfigHandler[]
		 */
		protected $tConfigHandlerList = [];
		/**
		 * @var bool
		 */
		protected $tInit = false;
		/**
		 * @var bool
		 */
		protected $tWarmup = false;
		/**
		 * @var bool
		 */
		protected $tConfig = false;
		/**
		 * @var bool
		 */
		protected $tSetup = false;

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
				return $this;
			}
			$this->tInit = true;
			$this->handleInit();
			return $this;
		}

		protected function handleInit() {
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
		public function warmup() {
			if ($this->tWarmup) {
				return $this;
			}
			$this->tWarmup = true;
			$this->init();
			$this->handleWarmup();
			return $this;
		}

		protected function handleWarmup() {
		}

		/**
		 * @inheritdoc
		 */
		public function isWarmedup(): bool {
			return $this->tWarmup;
		}

		/**
		 * @inheritdoc
		 */
		public function config() {
			if ($this->tConfig) {
				return $this;
			}
			$this->tConfig = true;
			$this->warmup();
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

		/**
		 * @inheritdoc
		 */
		public function setup() {
			if ($this->tSetup) {
				return $this;
			}
			$this->tSetup = true;
			$this->config();
			$this->handleSetup();
			return $this;
		}

		protected function handleSetup() {
		}

		/**
		 * @inheritdoc
		 */
		public function isSetup(): bool {
			return $this->tSetup;
		}
	}
