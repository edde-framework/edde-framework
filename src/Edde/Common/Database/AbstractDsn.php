<?php
	declare(strict_types=1);

	namespace Edde\Common\Database;

	use Edde\Api\Database\IDsn;
	use Edde\Common\Object;

	abstract class AbstractDsn extends Object implements IDsn {
		protected $optionList = [];

		/**
		 * @inheritdoc
		 */
		public function setOption(string $option, $value): IDsn {
			$this->optionList[$option] = $value;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getOption(string $option, $default = null) {
			return $this->optionList[$option] ?? $default;
		}

		/***
		 * @inheritdoc
		 */
		public function getOptionList(): array {
			return $this->optionList;
		}
	}
