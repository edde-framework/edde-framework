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
		 * @param IMacro     $source
		 * @param ITemplate  $template
		 * @param \Iterator  $iterator
		 * @param INode      $node
		 * @param string     $name
		 * @param mixed|null $value
		 */
		public function inline(IMacro $source, ITemplate $template, \Iterator $iterator, INode $node, string $name, $value = null);

		/**
		 * register macro event around enter/node/leave
		 *
		 * @param mixed    $event
		 * @param callable $callback
		 *
		 * @return IMacro
		 */
		public function on($event, callable $callback): IMacro;
	}
