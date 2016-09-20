<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\IMacro;
	use Edde\Common\AbstractObject;

	abstract class AbstractMacro extends AbstractObject implements IMacro {
		/**
		 * @var string[]
		 */
		protected $macroList = [];

		/**
		 * @param string[] $macroList
		 */
		public function __construct(array $macroList) {
			$this->macroList = $macroList;
		}

		public function getMacroList(): array {
			return $this->macroList;
		}
	}
