<?php
	declare(strict_types = 1);
	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
	use Edde\Common\AbstractObject;

	abstract class AbstractMacro extends AbstractObject implements IMacro {
		/**
		 * @var array
		 */
		protected $macroList = [];

		/**
		 * @param array $macroList
		 */
		public function __construct(array $macroList) {
			$this->macroList = $macroList;
		}

		public function getMacroList(): array {
			return $this->macroList;
		}

		public function run(INode $root, ...$parameterList) {
			return call_user_func_array([
				$this,
				'execute',
			], array_merge([
				$root,
			], $parameterList));
		}
	}
