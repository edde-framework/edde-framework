<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	use Edde\Api\Node\INode;

	interface IMacro {
		/**
		 * register to the template all supported "names" related to this macro
		 *
		 * @param ITemplate $template
		 *
		 * @return IMacro
		 */
		public function register(ITemplate $template): IMacro;

		/**
		 * return list of names to register
		 *
		 * @return string[]
		 */
		public function getNameList(): array;

		/**
		 * when there is inline node detected over the macro
		 *
		 * @param INode $node
		 *
		 * @return IMacro
		 */
		public function inline(INode $node): IMacro;

		/**
		 * main macro execution
		 *
		 * @param INode $node
		 *
		 * @return IMacro
		 */
		public function macro(INode $node): IMacro;
	}
