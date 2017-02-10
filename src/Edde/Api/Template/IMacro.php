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
		 * @param ITemplate $template
		 * @param INode     $node
		 * @param string    $name
		 * @param string    $value
		 *
		 * @return
		 */
		public function inline(ITemplate $template, INode $node, string $name, string $value = null);

		/**
		 * open macro event (initial macro code)
		 *
		 * @param ITemplate $template
		 * @param INode     $node
		 */
		public function open(ITemplate $template, INode $node);

		/**
		 *
		 *
		 * @param ITemplate $template
		 * @param INode     $node
		 */
		public function macro(ITemplate $template, INode $node);

		/**
		 * ending macro code (end of macro code)
		 *
		 * @param ITemplate $template
		 * @param INode     $node
		 */
		public function close(ITemplate $template, INode $node);
	}
