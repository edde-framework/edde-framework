<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\IHelper;
	use Edde\Api\Template\IHelperSet;
	use Edde\Common\Usable\AbstractUsable;

	class HelperSet extends AbstractUsable implements IHelperSet {
		/**
		 * @var IHelper[]
		 */
		protected $helperList = [];

		public function registerHelper(IHelper $helper): IHelperSet {
			$this->helperList[] = $helper;
			return $this;
		}

		public function getHelperList(): array {
			$this->use();
			return $this->helperList;
		}

		protected function prepare() {
		}
	}