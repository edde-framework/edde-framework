<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Usable\AbstractUsable;

	class Template extends AbstractUsable implements ITemplate {
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;
		/**
		 * @var IMacro[]
		 */
		protected $macroList = [];
		/**
		 * @var array
		 */
		protected $variableList = [];

		/**
		 * @param IResourceManager $resourceManager
		 */
		public function __construct(IResourceManager $resourceManager) {
			$this->resourceManager = $resourceManager;
		}

		public function registerMacro(IMacro $macro): ITemplate {
			$this->macroList[] = $macro;
			return $this;
		}

		public function load(string $file, ...$parameterList) {
			$this->macro($this->resourceManager->file($file), ...$parameterList);
			return $this;
		}

		public function macro(INode $root, ...$parameterList) {
			$this->usse();
			if (isset($this->macroList[$nodeName = $root->getName()]) === false) {
				throw new TemplateException(sprintf('Unknown node [%s]; did you registered macro for it?', $nodeName));
			}
			return $this->macroList[$nodeName]->run($this, $root, ...$parameterList);
		}

		public function setVariable(string $name, $value): ITemplate {
			$this->variableList[$name] = $value;
			return $this;
		}

		public function setVariableList(array $variableList): ITemplate {
			$this->variableList = $variableList;
			return $this;
		}

		public function getVariable(string $name) {
			if (isset($this->variableList[$name]) === false) {
				throw new TemplateException(sprintf('Unknown variable [%s].', $name));
			}
			return $this->variableList[$name];
		}

		protected function prepare() {
			$macroList = $this->macroList;
			$this->macroList = [];
			foreach ($macroList as $macro) {
				foreach ($macro->getMacroList() as $name) {
					$this->macroList[$name] = $macro;
				}
			}
		}
	}
