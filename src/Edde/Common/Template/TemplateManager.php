<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\File\File;
	use Edde\Common\Usable\AbstractUsable;

	class TemplateManager extends AbstractUsable implements ITemplateManager {
		use LazyInjectTrait;
		use CacheTrait;
		/**
		 * @var ITemplateDirectory
		 */
		protected $templateDirectory;
		/**
		 * @var IRootDirectory
		 */
		protected $rootDirectory;
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;
		/**
		 * @var IMacro[]
		 */
		protected $macroList = [];

		public function lazyTemplateDiretory(ITemplateDirectory $templateDirectory) {
			$this->templateDirectory = $templateDirectory;
		}

		public function lazyRootDiretory(IRootDirectory $rootDirectory) {
			$this->rootDirectory = $rootDirectory;
		}

		public function lazyResourceManager(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}

		public function registerMacroList(array $macroList): ITemplateManager {
			$this->macroList = array_merge($this->macroList, $macroList);
			return $this;
		}

		public function template(string $file, bool $force = false): ITemplate {
			return $this->compile(new File($file), $force);
		}

		public function compile(IFile $file, bool $force = false): ITemplate {
			$this->use();
			if (($templateFile = $this->cache->load($cacheId = $file->getPath(), false)) !== false && $force === false) {
				return new Template(new File($templateFile));
			}
			if ((($root = $this->resourceManager->resource($file)) instanceof INode) === false) {
				throw new TemplateException(sprintf('Resource handler for [%s] must return [%s].', (string)$file->getUrl(), INode::class));
			}
			$compiler = new Compiler($root, $this->rootDirectory, $file, $templateFile = $this->templateDirectory->file(($name = ('Template_' . sha1((string)$file->getUrl()))) . '.php'), $name);
			$compiler->registerMacroList($this->macroList);
			$this->cache->save($cacheId, $templateFile->getPath());
			return $compiler->compile();
		}

		protected function prepare() {
			$this->cache();
			$this->templateDirectory->create();
		}
	}
