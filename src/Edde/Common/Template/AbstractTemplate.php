<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\Filter\IFilter;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\Filter\ActionAttributeFilter;
	use Edde\Common\Template\Filter\ClassAttributeFilter;
	use Edde\Common\Template\Filter\ValueAttributeFilter;
	use Edde\Common\Usable\AbstractUsable;
	use ReflectionClass;
	use ReflectionMethod;

	abstract class AbstractTemplate extends AbstractUsable implements ITemplate {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IResourceManager
		 */
		protected $resourceManager;
		/**
		 * @var string[]
		 */
		protected $nodeList = [];
		/**
		 * node attribute/meta filters
		 *
		 * @var IFilter[]
		 */
		protected $filterList = [];

		/**
		 * @param IContainer $container
		 * @param IResourceManager $resourceManager
		 */
		public function __construct(IContainer $container, IResourceManager $resourceManager) {
			$this->container = $container;
			$this->resourceManager = $resourceManager;
		}

		public function registerFilter(string $name, IFilter $filter): ITemplate {
			$this->filterList[$name] = $filter;
			return $this;
		}

		public function build(string $file, IControl $control): ITemplate {
			$this->usse();
			$root = $this->resourceManager->file($file);
			if ($root->getName() !== 'template') {
				throw new TemplateException(sprintf('Template [%s] contains unknown root node [%s].', $file, $root->getName()));
			}
			$this->control($root, $control);
			return $this;
		}

		protected function control(INode $node, IControl $control) {
			if (isset($this->nodeList[$nodeName = $node->getName()]) === false) {
				throw new TemplateException(sprintf('Unknon node [%s]; did you used "use" node?', $nodeName));
			}
			$this->nodeList[$nodeName]($node, $control);
		}

		protected function nodeTemplate(INode $root, IControl $control) {
			foreach ($root->getNodeList() as $node) {
				$this->control($node, $control);
			}
		}

		protected function nodeUse(INode $root, IControl $parent) {
			$this->nodeList[$root->getAttribute('name')] = function (INode $node, IControl $parent) use ($root) {
				/** @var $control IControl */
				$control = $this->container->create($root->getAttribute('control'));
				$parent->addControl($control);
				$controlNode = $control->getNode();
				$metaList = $controlNode->getMetaList();
				$attributeList = $controlNode->getAttributeList();
				$controlNode->addMetaList($node->getMetaList());
				foreach ($node->getAttributeList() as $name => $value) {
					if (isset($this->filterList[$name]) && ($value = $this->filterList[$name]->filter($value, $control, $node)) === false) {
						continue;
					}
					$controlNode->setAttribute($name, $value);
				}
				/**
				 * it's important to keep the original values which cannot be changed
				 */
				$node->addAttributeList($attributeList);
				$node->addMetaList($metaList);
				foreach ($node->getNodeList() as $child) {
					$this->control($child, $control);
				}
			};
		}

		protected function prepare() {
			$reflectionClass = new ReflectionClass($this);
			foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PROTECTED) as $reflectionMethod) {
				if (strpos($reflectionMethod->getName(), 'node') === false) {
					continue;
				}
				$name = StringUtils::recamel(str_replace('node', null, $reflectionMethod->getName()));
				$this->nodeList[$name] = [
					$this,
					$reflectionMethod->getName(),
				];
			}
			$this->filterList = [
				'class' => new ClassAttributeFilter(),
				'action' => new ActionAttributeFilter(),
				'value' => new ValueAttributeFilter(),
			];
		}
	}
