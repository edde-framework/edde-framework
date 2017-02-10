<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateProvider;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	abstract class AbstractTemplate extends Object implements ITemplate {
		use ConfigurableTrait;

		/**
		 * @var IMacro[]
		 */
		protected $macroList;
		/**
		 * @var ITemplateProvider
		 */
		protected $templateProvider;
		/**
		 * @var IResource[]
		 */
		protected $resourceList;
		/**
		 * @var INode[]
		 */
		protected $blockList = [];

		/**
		 * @inheritdoc
		 */
		public function registerMacro(string $name, IMacro $macro): ITemplate {
			$this->macroList[$name] = $macro;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function registerTemplateProvider(ITemplateProvider $templateProvider): ITemplate {
			$this->templateProvider = $templateProvider;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function import(string $name, IResource $resource): ITemplate {
			$this->resourceList[$name] = $resource;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function block(string $name, INode $node): ITemplate {
			$this->blockList[$name] = $node;
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getBlock(string $name, INode $node): INode {
			if (isset($this->blockList[$name]) === false) {
				throw new MacroException(sprintf('Requested unknown block [%s] on macro [%s].', $name, $node->getPath()));
			}
			return $this->blockList[$name];
		}

		protected function inline(INode $node, string $name, string $value = null) {
			if (isset($this->macroList[$name]) === false) {
				throw new MacroException(sprintf('Unknown inline macro [%s] on node [%s].', $name, $node->getPath()));
			}
			$this->macroList[$name]->inline($this, $node, $name, $value);
		}

		public function getMacro(INode $node) {
			if (isset($this->macroList[$name = $node->getName()]) === false) {
				throw new MacroException(sprintf('Unknown macro [%s] on node [%s].', $name, $node->getPath()));
			}
			return $this->macroList[$name];
		}
	}
