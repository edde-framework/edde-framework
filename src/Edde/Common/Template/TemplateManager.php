<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
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
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Usable\AbstractUsable;

	class TemplateManager extends AbstractUsable implements ITemplateManager {
		use CacheTrait;
		/**
		 * @var IContainer
		 */
		protected $container;
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

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

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

		public function template(string $file, ...$parameterList): ITemplate {
			return $this->instance($this->compile(new File($file)), $parameterList);
		}

		public function instance(IFile $file, array $parameterList = []) {
			if (class_exists($class = str_replace('.php', '', $file->getName())) === false) {
				(function (IFile $file) {
					require_once($file->getPath());
				})($file);
			}
			return $this->container->create($class, ...$parameterList);
		}

		public function compile(IFile $file, bool $force = false): IFile {
			$this->use();
			if ($file->isAvailable() === false) {
				throw new TemplateException(sprintf('Template file [%s] is not available.', $file->getPath()));
			}
			if (($templateFile = $this->cache->load($cacheId = $file->getPath(), false)) !== false && $force === false) {
				return new File($templateFile);
			}
			$compiler = new Compiler($this->update($this->process($this->load($file), $file)), $this->rootDirectory, $this->assetsDirectory, $file, $templateFile = $this->templateDirectory->file(($name = ('Template_' . sha1((string)$file->getUrl()))) . '.php'), $name);
			$macroList = [];
			foreach ($this->macroList as $macro) {
				$macroList[] = clone $macro;
			}
			$compiler->registerMacroList($macroList);
			$this->cache->save($cacheId, $templateFile->getPath());
			return $compiler->compile();
		}

		protected function update(INode $root) {
			foreach (($iterator = NodeIterator::recursive($root, true)) as $node) {
				if ($node->hasAttributeList('m') === false) {
					continue;
				}
				$attributeList = $node->getAttributeList('m');
				$node->removeAttributeList('m');
				foreach (array_reverse($attributeList, true) as $attribute => $value) {
					$macro = (new Node($attribute, $value))->setMeta('inline', true);
					if ($node->isRoot()) {
						$node->insert($macro);
						continue;
					}
					$node = $node->switch($macro);
				}
				$iterator->rewind();
			}
			return $root;
		}

		protected function process(INode $root, IFile $source): INode {
			foreach (NodeIterator::recursive($root, true) as $node) {
				if ($node->hasAttributeList('x')) {
					$processList = $node->getAttributeList('x');
					$attributeList = $node->getAttributeList();
					foreach ($processList as $process => $value) {
						unset($attributeList['x:' . $process]);
						$node->setAttributeList($attributeList);
						switch ($process) {
							case 'include':
								$this->process($this->resourceManager->resource($file = new File($this->delimite($value, $source)), null, $node), $file);
								break;
						}
					}
					$node->removeAttributeList('x');
					continue;
				}
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
			return $root;
		}

		protected function delimite(string $value, IFile $source): string {
			if (strpos($value, './', 0) !== false) {
				return $source->getDirectory()
					->filename(str_replace('./', '', $value));
			}
			return $value;
		}

		protected function load(IFile $file) {
			if ((($node = $this->resourceManager->resource($file)) instanceof INode) === false) {
				throw new TemplateException(sprintf('Resource handler for [%s] must return [%s].', (string)$file->getUrl(), INode::class));
			}
			return $node;
		}

		protected function prepare() {
			$this->templateDirectory->create();
			$this->cache();
		}
	}
