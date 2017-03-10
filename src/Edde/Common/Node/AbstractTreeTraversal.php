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
		 * @var INode
		 */
		protected $root;

		/**
		 * So Little Johnny's teacher is warned at the beginning of the school year not to ever make a bet with Johnny unless she is absolutely sure she will win it.
		 * One day in class, Johnny raises his hand and says "teacher, I'll bet you $50 I can guess what color your underwear is."
		 * She replies, "okay, meet me after class and we'll settle it." But beforeclass ends, she goes to the restroom and removes her panties.
		 * After class is over and the studentsclear out, Johnny makes his guess.
		 * "Blue."
		 * "Nope. You got it wrong," she says as she lifts her skirt to reveal she isn't wearing any underwear.
		 * "Well come with me out to my dads car, he's waiting for me, and I'll get you the money." She follows him out.
		 * When they get to the car she informs his dad that he got the bet wrong and that she showed Johnny that she wasn't wearing any underwear.
		 * His dad exclaims: "That mother fucker! He bet me $100 this morning that he'd see your pussy before the end of the day!"
		 *
		 * @param INode $root
		 */
		public function __construct(INode $root) {
			$this->root = $root;
		}

		/**
		 * @inheritdoc
		 */
		public function traverse(INode $node): ITreeTraversal {
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function open(INode $node, ...$parameters) {
		}

		/**
		 * @inheritdoc
		 */
		public function content(INode $node, \Iterator $iterator, ...$parameters) {
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
				$treeTraversal = $this->traverse($current);
				foreach ($stack as list($levelTreeTraversal, $levelCurrent)) {
					if ($levelCurrent->getLevel() < $currentLevel) {
						break;
					}
					$levelTreeTraversal->close($levelCurrent, ...$parameters);
					$stack->pop();
				}
				if ($current->isLeaf() === false) {
					$stack->push([
						$treeTraversal,
						$current,
					]);
				}
				$treeTraversal->open($current, ...$parameters);
				($treeTraversal->content($current, $iterator, ...$parameters));
				if ($current->isLeaf()) {
					$treeTraversal->close($current, ...$parameters);
				}
				$iterator->next();
			}
			while ($stack->isEmpty() === false) {
				list($levelTreeTraversal, $levelCurrent) = $stack->pop();
				$levelTreeTraversal->close($levelCurrent, ...$parameters);
			}
		}

		/**
		 * @inheritdoc
		 */
		public function close(INode $node, ...$parameters) {
		}
	}
