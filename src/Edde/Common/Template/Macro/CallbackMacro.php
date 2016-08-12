<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Common\Template\AbstractMacro;

	class CallbackMacro extends AbstractMacro {
		/**
		 * @var callable
		 */
		protected $callback;

		/**
		 * @param array $macroList
		 * @param callable $callback
		 */
		public function __construct(array $macroList, callable $callback) {
			parent::__construct($macroList);
			$this->callback = $callback;
		}

		public function run(INode $root, ...$parameterList) {
			return call_user_func_array($this->callback, array_merge([
				$root,
			], $parameterList));
		}
	}
