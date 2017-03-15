<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Api\Template\LazyTemplateDirectoryTrait;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Node\NodeUtils;
	use Edde\Common\Strings\StringUtils;

	class Template extends AbstractTemplate implements ILazyInject {
		use LazyTemplateDirectoryTrait;
		use LazyResourceManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function compile(string $name, IResource $resource): IFile {
			if (empty($this->resourceList)) {
				throw new TemplateException(sprintf('Resource list is empty;cannot build a template.'));
			}
			ob_start();
			NodeUtils::namespace($root = $this->resourceManager->resource($resource), '~^(?<namespace>[a-z]):(?<name>[a-zA-Z0-9_-]+)$~');
			$iterator = NodeIterator::recursive($root);
			$iterator->rewind();
			$this->traverse($root, $iterator, $this);
			$file = $this->templateDirectory->file('snippet-' . StringUtils::recamel($name) . '.php');
			$file->write(ob_get_clean());
			$file->close();
			return $file;
		}
		// public function execute($context = null) {
		// 	$file = $this->compile();
		// 	$context = is_array($context) ? $context : [null => $context];
		// 	include $file->getPath();
		// }
	}
