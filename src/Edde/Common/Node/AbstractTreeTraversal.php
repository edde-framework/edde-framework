<?php
	declare(strict_types=1);

	namespace Edde\Common\Node;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\ITreeTraversal;
	use Edde\Common\Object;

	/**
	 * A new teacher was trying to make use of her psychology courses.
	 * She started her class by saying, "Everyone who thinks they're stupid, stand up!"
	 * After a few seconds, Little Johnny stood up.
	 * The teacher said, "Do you think you're stupid, Little Johnny?"
	 * "No, ma'am, but I hate to see you standing there all by yourself!"
	 */
	abstract class AbstractTreeTraversal extends Object implements ITreeTraversal {
		/**
		 * @inheritdoc
		 */
		public function traverse(INode $node, ...$parameters): ITreeTraversal {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function enter(INode $node, ...$parameters) {
		}

		/**
		 * @inheritdoc
		 */
		public function node(INode $node, \Iterator $iterator, ...$parameters) {
			$level = $node->getLevel();
			$stack = new \SplStack();
			/**
			 * @var $levelTreeTraversal ITreeTraversal
			 * @var $levelCurrent       INode
			 * @var $current            INode
			 */
			while ($iterator->valid() && $current = $iterator->current()) {
				/**
				 * we are out ot current subtree
				 */
				if (($currentLevel = $current->getLevel()) <= $level) {
					break;
				}
				$treeTraversal = $this->traverse($current, ...$parameters);
				foreach ($stack as list($levelTreeTraversal, $levelCurrent)) {
					if ($levelCurrent->getLevel() < $currentLevel) {
						break;
					}
					$levelTreeTraversal->leave($levelCurrent, ...$parameters);
					$stack->pop();
				}
				if ($current->isLeaf() === false) {
					$stack->push([
						$treeTraversal,
						$current,
					]);
				}
				$treeTraversal->enter($current, ...$parameters);
				$treeTraversal->node($current, $iterator, ...$parameters);
				if ($current->isLeaf()) {
					$treeTraversal->leave($current, ...$parameters);
				}
				$iterator->next();
			}
			while ($stack->isEmpty() === false) {
				list($levelTreeTraversal, $levelCurrent) = $stack->pop();
				$levelTreeTraversal->leave($levelCurrent, ...$parameters);
			}
		}

		/**
		 * @inheritdoc
		 */
		public function leave(INode $node, ...$parameters) {
		}
	}
