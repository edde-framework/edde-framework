<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\IMacroSet;
	use Edde\Common\Usable\AbstractUsable;

	class MacroSet extends AbstractUsable implements IMacroSet {
		/**
		 * @var IMacro[]
		 */
		protected $macroList = [];

		public function getMacroList(): array {
			$this->use();
			return $this->macroList;
		}

		public function setMacroList(array $macroList) {
			$this->macroList = [];
			foreach ($macroList as $macro) {
				$this->registerMacro($macro);
			}
			return $this;
		}

		public function registerMacro(IMacro $macro): IMacroSet {
			$this->macroList[$macro->getName()] = $macro;
			return $this;
		}

		protected function prepare() {
		}
	}
