<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Node\NodeIterator;

	class Template extends AbstractTemplate {
		use LazyResourceManagerTrait;

		protected function template(INode $root) {
			foreach (NodeIterator::recursive($root, true) as $node) {
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
