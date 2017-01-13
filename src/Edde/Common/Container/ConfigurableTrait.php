<?php
	declare(strict_types=1);

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
		public function init(bool $force = false) {
			if ($this->tInit && $force === false) {
				return $this;
			}
			$this->tInit = true;
			$this->handleInit();
			return $this;
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
		public function warmup(bool $force = false) {
			if ($this->tWarmup && $force === false) {
				return $this;
			}
			$this->tWarmup = true;
			$this->init($force);
			$this->handleWarmup();
			return $this;
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
		public function config(bool $force = false) {
			if ($this->tConfig && $force === false) {
				return $this;
			}
			$this->tConfig = true;
			$this->warmup($force);
			foreach ($this->tConfigHandlerList as $configHandler) {
				$configHandler->config($this);
			}
			$this->handleConfig();
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
		public function setup(bool $force = false) {
			if ($this->tSetup && $force === false) {
				return $this;
			}
			$this->tSetup = true;
			$this->config($force);
			$this->handleSetup();
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function isSetup(): bool {
			return $this->tSetup;
		}
	}
