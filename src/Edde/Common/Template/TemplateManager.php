<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
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

		public function compile(IFile $file): ITemplate {
			$this->usse();
			if ((($root = $this->resourceManager->resource($file)) instanceof INode) === false) {
				throw new TemplateException(sprintf('Resource handler for [%s] must return [%s].', (string)$file->getUrl(), INode::class));
			}
			$template = new Template($templateFile = $this->templateDirectory->file(($name = ('Template' . sha1((string)$file->getUrl()))) . '.php'));
			$templateFile->enableWriteCache(3);
			$templateFile->write("<?php\n");
			$templateFile->write("\tdeclare(strict_types = 1);\n\n");
			$templateFile->write(sprintf("\tclass %s {\n", $name));
			try {
				$this->macro($root, $template, $file);
			} catch (TemplateException $e) {
				throw new TemplateException(sprintf('Compilation of template [%s] failed: %s', (string)$file->getUrl(), $e->getMessage()), 0, $e);
			}
			$templateFile->write("\t}\n");
			$templateFile->close();
			return $template;
		}

		public function macro(INode $root, ITemplate $template, IFile $file, ...$parameterList) {
			if (isset($this->macroList[$name = $root->getName()]) === false) {
				throw new TemplateException(sprintf('Unknown macro [%s].', $root->getPath()));
			}
			if ($root->hasAttributeList('m')) {
				$attributeList = $root->getAttributeList();
				foreach ($root->getAttributeList('m') as $attribute => $value) {
					unset($attributeList['m:' . $attribute]);
					$root->setAttributeList($attributeList);
					$this->macro((new Node('m:' . $attribute, $value))->addNode($root), $template, $file);
				}
				return;
			}
			$this->macroList[$name]->run($this, $template, $root, $file, ...$parameterList);
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
