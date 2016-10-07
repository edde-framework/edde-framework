<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Node\Node;

	/**
	 * Shortcut for multiple nodes injection into some method (variable pass makes no sense).
	 */
	class PassChildMacro extends AbstractHtmlMacro {
		/**
		 * Profanity is the one language that all programmers know best.
		 */
		public function __construct() {
			parent::__construct('pass-child');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function inline(INode $macro, ICompiler $compiler) {
			return $this->insert($macro, 'target');
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function compile(INode $macro, ICompiler $compiler) {
			$target = $this->extract($macro, 'target');
			foreach ($macro->getNodeList() as $node) {
				/** @var $nodeList INode[] */
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
