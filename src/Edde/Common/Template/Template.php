<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\IAttributeList;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Node\NodeUtils;

	class Template extends AbstractTemplate {
		use LazyResourceManagerTrait;

		protected function namespace(INode $node, IAttributeList $attributeList) {
			foreach ($attributeList->get('t', []) as $k => $v) {
				$this->inline($node, $k, $v);
			}
		}

		protected function template(INode $root) {
			NodeUtils::namespace($root, '~^(?<namespace>[a-z]):(?<name>[a-zA-Z0-9_-]+)$~');
			$stack = new \SplStack();
			$level = -1;
			foreach (NodeIterator::recursive($root, true) as $node) {
				$attributeList = $node->getAttributeList();
				/**
				 * there are some fucking macros, oops!
				 */
				$this->namespace($node, $attributeList);
				if ($node->getLevel() < $level) {
					/** @var $macro IMacro */
					list($macro, $close) = $stack->pop();
					$macro->close($this, $close);
				}
				$macro = $this->getMacro($node);
				$macro->open($this, $node);
				$macro->macro($this, $node);
				$level = $node->getLevel();
				if ($node->isLeaf() === false) {
					$stack->push([
						$macro,
						$node,
					]);
					continue;
				}
				$macro->close($this, $node);
			}
			while ($stack->isEmpty() === false) {
				/** @var $macro IMacro */
				list($macro, $close) = $stack->pop();
				$macro->close($this, $close);
			}
		}

		/**
		 * @inheritdoc
		 */
		public function compile() {
			if (empty($this->resourceList)) {
				throw new TemplateException(sprintf('Resource list is empty;cannot build a template.'));
			}
			foreach ($this->resourceList as $resource) {
				$this->template($this->resourceManager->resource($resource));
			}
		}
	}
