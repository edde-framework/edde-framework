<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Api\Template\LazyTemplateManagerTrait;
	use Edde\Api\Template\LazyTemplateProviderTrait;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Node\NodeUtils;

	class Template extends AbstractTemplate implements ILazyInject {
		use LazyTemplateManagerTrait;
		use LazyTemplateProviderTrait;
		use LazyResourceManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function compile(): IFile {
			if (empty($this->resourceList)) {
				throw new TemplateException(sprintf('Resource list is empty;cannot build a template.'));
			}
			ob_start();
			foreach ($this->resourceList as $resource) {
				NodeUtils::namespace($root = $this->resourceManager->resource($resource), '~^(?<namespace>[a-z]):(?<name>[a-zA-Z0-9_-]+)$~');
				$iterator = NodeIterator::recursive($root);
				$iterator->rewind();
				$this->traverse($root, $iterator, $this);
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
