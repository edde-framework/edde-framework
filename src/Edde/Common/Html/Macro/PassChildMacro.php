<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Crypt\LazyCryptEngineTrait;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	/**
	 * Shortcut for multiple nodes injection into some method (variable pass makes no sense).
	 */
	class PassChildMacro extends AbstractHtmlMacro {
		use LazyCryptEngineTrait;

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
		public function macro(INode $macro, ICompiler $compiler) {
			parent::macro($macro, $compiler);
//			$target = $this->attribute($macro, $compiler, 'target', false);
//			$func = substr($target, -2) === '()';
//			$target = str_replace('()', '', $target);
//			$type = $target[0];
//			$target = StringUtils::camelize(substr($target, 1), null, true);
//			$write = sprintf('%s->%s($stack->top());', $this->reference($macro, $type), $target);
//			if ($func === false) {
//				$write = sprintf('%s::setProperty(%s, %s, $stack->top());', ReflectionUtils::class, $this->reference($macro, $type), var_export($target, true));
//			}
//			foreach ($macro->getNodeList() as $node) {
//				if ($node->getMeta('snippet', false)) {
//					continue;
//				}
//				$compiler->macro($node);
//				$this->write($macro, $compiler, sprintf('%s', $write), 5);
//			}
//			$target = $this->extract($macro, 'target');
//			foreach ($macro->getNodeList() as $node) {
//				/** @var $nodeList INode[] */
//				$nodeList = [$node];
//				while ($node->getMeta('root', false) && $node->isLeaf() === false) {
//					$nodeList = $node->getNodeList();
//					$node = $node->getNodeList()[0];
//				}
//				foreach ($nodeList as $child) {
//					$child->prepend(new Node('pass', null, ['target' => $target]));
//				}
//			}
		}
	}
