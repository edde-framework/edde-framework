<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Inline;

	use Edde\Common\Node\Node;

	/**
	 * Shortcut for multiple nodes injection into some method (variable pass makes no sense).
	 */
	class PassChildInline extends AbstractHtmlInline {
		/**
		 * Profanity is the one language that all programmers know best.
		 */
		public function __construct() {
			parent::__construct('m:pass-child', true);
		}

		protected function onMacro() {
			$target = $this->extract($this->macro, $this->getName(), null, false);
			foreach ($this->macro->getNodeList() as $node) {
				$nodeList = [$node];
				while ($node->getMeta('root', false) && $node->isLeaf() === false) {
					$nodeList = $node->getNodeList();
					$node = $node->getNodeList()[0];
				}
				foreach ($nodeList as $child) {
					$child->prepend(new Node('pass', null, ['target' => $target]));
				}
			}
		}
	}
