<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Filter\IFilter;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Template\AbstractMacro;

	/**
	 * Register available controls.
	 */
	class ControlMacro extends AbstractMacro {
		/**
		 * @var IContainer
		 */
		protected $container;
		protected $controlList = [];
		/**
		 * node attribute filters
		 *
		 * @var IFilter[]
		 */
		protected $filterList = [];

		public function __construct(IContainer $container) {
			parent::__construct([]);
			$this->container = $container;
		}

		public function registerControlList(array $controlList) {
			$this->controlList = array_merge($this->controlList, $controlList);
			return $this;
		}

		public function registerFilterList(array $filterList) {
			$this->filterList = array_merge($this->filterList, $filterList);
			return $this;
		}

		public function getMacroList(): array {
			return array_keys($this->controlList);
		}

		public function execute(ITemplate $template, INode $root, IHtmlControl $htmlControl) {
			/** @var $control IHtmlControl */
			$htmlControl->addControl($control = $this->container->create($this->controlList[$root->getName()]));
			$node = $control->getNode();
			$metaList = $node->getMetaList();
			$node->addMetaList($root->getMetaList());
			$attributes = [];
			foreach ($root->getAttributeList() as $name => $value) {
				if (isset($this->filterList[$name]) && ($value = $this->filterList[$name]->filter($value, $control, $root)) === false) {
					continue;
				}
				$attributes[$name] = $value;
			}
			/**
			 * it's important to keep the original values which cannot be changed
			 */
			$node->setAttributeList(array_merge_recursive($node->getAttributeList(), $attributes));
			$node->addMetaList($metaList);
			foreach ($root->getNodeList() as $node) {
				$template->macro($node, $control);
			}
			return $control;
		}
	}
