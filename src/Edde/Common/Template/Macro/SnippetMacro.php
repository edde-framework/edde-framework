<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
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
			ob_start();
			$level = $node->getLevel();
			$stack = new \SplStack();
			/** @var $levelMacro IMacro */
			/** @var $levelCurrent INode */
			/** @var $current INode */
			while ($iterator->valid() && $current = $iterator->current()) {
				/**
				 * we are out ot current subtree
				 */
				if (($currentLevel = $current->getLevel()) <= $level) {
					break;
				}
				$macro = $template->getMacro($current);
				foreach ($stack as list($levelMacro, $levelCurrent)) {
					if ($levelCurrent->getLevel() < $currentLevel) {
						break;
					}
					$levelMacro->close($template, $levelCurrent);
					$stack->pop();
				}
				if ($current->isLeaf() === false) {
					$stack->push([
						$macro,
						$current,
					]);
				}
				$macro->open($template, $current);
				$macro->macro($template, $current, $iterator);
				if ($current->isLeaf()) {
					$macro->close($template, $current);
				}
				$iterator->next();
			}
			while ($stack->isEmpty() === false) {
				list($levelMacro, $levelCurrent) = $stack->pop();
				$levelMacro->close($template, $levelCurrent);
			}
			$source = ob_get_clean();
		}
	}
