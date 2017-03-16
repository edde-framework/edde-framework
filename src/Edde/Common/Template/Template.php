<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\LazyResourceManagerTrait;
	use Edde\Api\Template\LazyTemplateDirectoryTrait;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Node\NodeUtils;
	use Edde\Common\Strings\StringUtils;

	class Template extends AbstractTemplate implements ILazyInject {
		use LazyTemplateDirectoryTrait;
		use LazyResourceManagerTrait;
		use CacheTrait;

		/**
		 * @inheritdoc
		 */
		public function compile(string $name, IResource $resource): IFile {
			$cache = $this->cache();
			$file = $this->templateDirectory->file('snippet-' . StringUtils::recamel($name) . '.php');
			if ($cache->load($cacheId = ('template-' . $name)) && $file->isAvailable()) {
				return $file;
			}
			ob_start();
			NodeUtils::namespace($root = $this->resourceManager->resource($resource), '~^(?<namespace>[a-z]):(?<name>[a-zA-Z0-9_-]+)$~');
			$iterator = NodeIterator::recursive($root);
			$iterator->rewind();
			$this->traverse($root, $iterator, $this);
			$file->write(ob_get_clean());
			$file->close();
			$cache->save($cacheId, true);
			return $file;
		}
	}
