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

		public function getInlineList(): array {
			$this->use();
			return $this->inlineList;
		}

		public function setInlineList(array $inlineList) {
			$this->inlineList = [];
			foreach ($inlineList as $inline) {
				$this->registerInline($inline);
			}
			return $this;
		}

		public function registerInline(IInline $inline): IMacroSet {
			$this->inlineList[$inline->getName()] = $inline;
			return $this;
		}

		protected function prepare() {
		}
	}
