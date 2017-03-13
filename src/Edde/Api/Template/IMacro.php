<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\ITreeTraversal;

	interface IMacro extends ITreeTraversal {
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
		 * @param \Iterator $iterator
		 * @param INode     $node
		 * @param null      $value
		 */
		public function inline(ITemplate $template, \Iterator $iterator, INode $node, $value = null);
	}
