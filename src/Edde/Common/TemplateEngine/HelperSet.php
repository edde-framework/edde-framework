<?php
	declare(strict_types = 1);

	namespace Edde\Common\TemplateEngine;

	use Edde\Api\TemplateEngine\IHelper;
	use Edde\Api\TemplateEngine\IHelperSet;
	use Edde\Common\Object;

	class HelperSet extends Object implements IHelperSet {
		/**
		 * @var IHelper[]
		 */
		protected $helperList = [];

		public function registerHelper(IHelper $helper): IHelperSet {
			$this->helperList[] = $helper;
			return $this;
		}

		public function getHelperList(): array {
			return $this->helperList;
		}
	}
