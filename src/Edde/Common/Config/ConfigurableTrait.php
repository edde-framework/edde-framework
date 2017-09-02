<?php
	declare(strict_types=1);

	namespace Edde\Common\Config;

	use Edde\Api\Config\ConfigException;
	use Edde\Api\Config\IConfigurator;

	trait ConfigurableTrait {
		/**
		 * @var IConfigurator[]
		 */
		protected $tConfiguratorList = [];
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
		public function addConfigurator(IConfigurator $configurator) {
			$this->tConfiguratorList[] = $configurator;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function setConfiguratorList(array $configuratorList) {
			$this->tConfiguratorList = $configuratorList;
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

		public function checkInit() {
			if ($this->tInit === false) {
				throw new ConfigException(sprintf('Class [%s] has not been initialized!', static::class));
			}
		}

		/**
		 * @inheritdoc
		 */
		public function config() {
			if ($this->tConfig) {
				return $this;
			}
			$this->tConfig = true;
			$this->init();
			foreach ($this->tConfiguratorList as $configHandler) {
				$configHandler->configure($this);
			}
			$this->handleConfig();
			return $this;
		}

		public function checkConfig() {
			if ($this->tConfig === false) {
				throw new ConfigException(sprintf('Class [%s] has not been configured!', static::class));
			}
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

		/**
		 * @inheritdoc
		 */
		public function isSetup(): bool {
			return $this->tSetup;
		}

		public function checkSetup() {
			if ($this->tSetup === false) {
				throw new ConfigException(sprintf('Class [%s] has not been set up!', static::class));
			}
		}
	}
