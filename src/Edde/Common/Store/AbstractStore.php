<?php
	declare(strict_types=1);

	namespace Edde\Common\Store;

	use Edde\Api\Store\IStore;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractStore extends Object implements IStore {
		use ConfigurableTrait;

		/**
		 * @inheritdoc
		 */
		public function lock(string $name = null, bool $block = true): IStore {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function unlock(string $name = null): IStore {
			return $this;
		}
	}
