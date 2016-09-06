<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\File\IFile;
	use Edde\Api\File\IRootDirectory;
	use Edde\Api\IAssetsDirectory;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\File\File;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Usable\AbstractUsable;

	class TemplateManager extends AbstractUsable implements ITemplateManager {
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
		 * @var IAssetsDirectory
		 */
		protected $assetsDirectory;
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

		public function lazyAssetsDirectory(IAssetsDirectory $assetsDirectory) {
			$this->assetsDirectory = $assetsDirectory;
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
			if ($file->isAvailable() === false) {
				throw new TemplateException(sprintf('Template file [%s] is not available.', $file->getPath()));
			}
			if (($templateFile = $this->cache->load($cacheId = $file->getPath(), false)) !== false && $force === false) {
				return new Template(new File($templateFile));
			}
			$this->use();
			if ((($root = $this->resourceManager->resource($file)) instanceof INode) === false) {
				throw new TemplateException(sprintf('Resource handler for [%s] must return [%s].', (string)$file->getUrl(), INode::class));
			}
			$this->process($root, $file);
			$compiler = new Compiler($root, $this->rootDirectory, $this->assetsDirectory, $file, $templateFile = $this->templateDirectory->file(($name = ('Template_' . sha1((string)$file->getUrl()))) . '.php'), $name);
			$macroList = [];
			foreach ($this->macroList as $macro) {
				$macroList[] = clone $macro;
			}
			$compiler->registerMacroList($macroList);
			$this->cache->save($cacheId, $templateFile->getPath());
			return $compiler->compile();
		}

		protected function process(INode $root, IFile $source): INode {
			foreach (NodeIterator::recursive($root) as $node) {
				if ($node->hasAttributeList('x')) {
					$processList = $node->getAttributeList('x');
					foreach ($processList as $process => $value) {
						switch ($process) {
							case 'include':
								$this->process($this->resourceManager->resource($file = new File($this->delimite($value, $source)), null, $node), $file);
								break;
						}
					}
					continue;
				}
				$node->removeAttributeList('x');
				if (strpos($name = $node->getName(), 'x:', 0) === false) {
					continue;
				}
				switch ($name) {
					case 'x:include':
						$root->replaceNode($node, $this->process($this->resourceManager->resource($file = new File($this->delimite($node->getAttribute('src'), $source)), null), $file)
							->getNodeList());
						break;
				}
			}
			$root->removeAttributeList('x');
			return $root;
		}

		protected function delimite(string $value, IFile $source): string {
			if (strpos($value, './', 0) !== false) {
				return $source->getDirectory()
					->filename(str_replace('./', '', $value));
			}
			return $value;
		}

		protected function prepare() {
			$this->templateDirectory->create();
		}
	}
