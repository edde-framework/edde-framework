<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Api\Template\LazyTemplateDirectoryTrait;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Node\NodeUtils;
	use Edde\Common\Strings\StringUtils;

	class Compiler extends AbstractCompiler implements ILazyInject {
		use LazyTemplateDirectoryTrait;
		use LazyResourceManagerTrait;

		/**
		 * @inheritdoc
		 */
		public function compile(string $name, IResource $resource): IFile {
			ob_start();
			NodeUtils::namespace($root = $this->resourceManager->resource($resource), '~^(?<namespace>[a-z]):(?<name>[a-zA-Z0-9_-]+)$~');
			$this->traverse($root, NodeIterator::recursive($root), $this);
			$file = $this->templateDirectory->file('snippet-' . StringUtils::recamel($name) . '.php');
			$file->write(ob_get_clean());
			$file->close();
			return $file;
		}
	}
