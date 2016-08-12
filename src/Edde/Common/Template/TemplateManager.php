<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateDirectory;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\File\File;
	use Edde\Common\Node\Node;
	use Edde\Common\Usable\AbstractUsable;

	class TemplateManager extends AbstractUsable implements ITemplateManager {
		use LazyInjectTrait;
		/**
		 * @var ITemplateDirectory
		 */
		protected $templateDirectory;
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

		public function lazyResourceManager(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}

		public function registerMacro(IMacro $macro): ITemplateManager {
			$this->macroList[] = $macro;
			return $this;
		}

		public function registerMacroList(array $macroList): ITemplateManager {
			$this->macroList = array_merge($this->macroList, $macroList);
			return $this;
		}

		public function template(string $file): ITemplate {
			return $this->compile(new File($file));
		}

		public function compile(IResource $resource): ITemplate {
			$this->usse();
			if ((($root = $this->resourceManager->resource($resource)) instanceof INode) === false) {
				throw new TemplateException(sprintf('Resource handler for [%s] must return [%s].', (string)$resource->getUrl(), INode::class));
			}
			$template = new Template($file = $this->templateDirectory->file(($name = ('Template' . sha1((string)$resource->getUrl()))) . '.php'));
			$file->write("<?php\n");
			$file->write("\tdeclare(strict_types = 1);\n\n");
			$file->write(sprintf("\tclass %s {\n", $name));
			try {
				$this->macro($root, $template, $resource);
			} catch (TemplateException $e) {
				throw new TemplateException(sprintf('Compilation of template [%s] failed: %s', (string)$resource->getUrl(), $e->getMessage()), 0, $e);
			}
			$file->write("\t}\n");
			$file->close();
			return $template;
		}

		public function macro(INode $node, ITemplate $template, IResource $resource, ...$parameterList) {
			if (isset($this->macroList[$name = $node->getName()]) === false) {
				throw new TemplateException(sprintf('Unknown macro [%s].', $node->getPath()));
			}
			if ($node->hasAttributeList('m')) {
				$attributeList = $node->getAttributeList();
				foreach ($node->getAttributeList('m') as $attribute => $value) {
					unset($attributeList['m:' . $attribute]);
					$node->setAttributeList($attributeList);
					$this->macro((new Node('m:' . $attribute, $value))->addNode($node), $template, $resource);
				}
				return;
			}
			$this->macroList[$name]->run($this, $template, $node, $resource, ...$parameterList);
		}

		protected function prepare() {
			$this->templateDirectory->create();
			$macroList = $this->macroList;
			$this->macroList = [];
			foreach ($macroList as $macro) {
				foreach ($macro->getMacroList() as $name) {
					$this->macroList[$name] = $macro;
				}
			}
		}
	}
