<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Template\AbstractMacro;

	class SnippetMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return ['snippet'];
		}

		/**
		 * @inheritdoc
		 */
		public function inline(ITemplate $template, INode $node, string $name, string $value = null) {
		}

		/**
		 * @inheritdoc
		 */
		public function macro(ITemplate $template, INode $node, \Iterator $iterator) {
			$level = $node->getLevel();
			$stack = new \SplStack();
			ob_start();
			while ($iterator->valid() && $current = $iterator->current()) {
				/**
				 * we are out ot current subtree
				 */
				if (($current->getLevel()) <= $level) {
					break;
				}
				$macro = $template->getMacro($current);
				$macro->open($template, $current);
				$macro->macro($template, $current, $iterator);
				$iterator->next();
			}
			while ($stack->isEmpty() === false) {
				// $file->write($stack->pop());
			}
			$source = ob_get_clean();
		}
	}
