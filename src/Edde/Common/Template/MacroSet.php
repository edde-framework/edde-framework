<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\IInline;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\IMacroSet;
	use Edde\Common\Usable\AbstractUsable;

	class MacroSet extends AbstractUsable implements IMacroSet {
		/**
		 * @var IMacro[]
		 */
		protected $macroList = [];
		/**
		 * @var IInline[]
		 */
		protected $inlineList = [];

		public function getMacroList(): array {
			$this->use();
			return $this->macroList;
		}

		public function setMacroList(array $macroList) {
			$this->macroList = $macroList;
			return $this;
		}

		public function getInlineList(): array {
			$this->use();
			return $this->inlineList;
		}

		public function setInlineList(array $inlineList) {
			$this->inlineList = $inlineList;
			return $this;
		}

		protected function prepare() {
		}
	}
