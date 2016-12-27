<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\IConfigHandler;

	trait ConfigurableTrait {
		/**
		 * @var IConfigHandler[]
		 */
		protected $configurableConfigHandlerList = [];
		protected $configurableConfig = false;

		/**
		 * @inheritdoc
		 */
		public function registerConfigHandlerList(array $configHandlerList) {
			$this->configurableConfigHandlerList = $configHandlerList;
			return $this;
		}

		public function config() {
			if ($this->configurableConfig) {
				return $this;
			}
			$this->configurableConfig = true;
			foreacH ($this->configurableConfigHandlerList as $configHandler) {
				$configHandler->config($this);
			}
			return $this;
		}
	}
