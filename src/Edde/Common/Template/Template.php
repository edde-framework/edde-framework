<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\LazyTemplateProviderTrait;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Node\NodeUtils;

	class Template extends AbstractTemplate implements ILazyInject {
		use LazyTemplateProviderTrait;
		use LazyResourceManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function compile() {
			if (empty($this->resourceList)) {
				throw new TemplateException(sprintf('Resource list is empty;cannot build a template.'));
			}
			ob_start();
			foreach ($this->resourceList as $resource) {
				NodeUtils::namespace($root = $this->resourceManager->resource($resource), '~^(?<namespace>[a-z]):(?<name>[a-zA-Z0-9_-]+)$~');
				$iterator = NodeIterator::recursive($root);
				$iterator->rewind();
				/** @var $macro IMacro */
				$macro = $this->traverse($root, $this);
				$macro->enter($root, $iterator, $this);
				$macro->node($root, $iterator, $this);
				$macro->leave($root, $iterator, $this);
			}
			$file = $this->getFile();
			$file->write(ob_get_clean());
			$file->close();
			return $file;
		}

		/**
		 * @inheritdoc
		 */
		public function execute($context = null) {
			$file = $this->compile();
			$context = is_array($context) ? $context : [null => $context];
			include $file->getPath();
		}
	}
