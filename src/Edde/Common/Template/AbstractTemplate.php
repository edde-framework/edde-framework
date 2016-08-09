<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\Filter\IFilter;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResourceManager;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\TemplateException;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\Filter\ActionAttributeFilter;
	use Edde\Common\Template\Filter\BindAttributeFilter;
	use Edde\Common\Template\Filter\ClassAttributeFilter;
	use Edde\Common\Template\Filter\PropertyAttributeFilter;
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
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var string[]
		 */
		protected $nodeList = [];
		/**
		 * @var ISchema[]
		 */
		protected $schemaList = [];
		/**
		 * node attribute/meta filters
		 *
		 * @var IFilter[]
		 */
		protected $filterList = [];

		/**
		 * @param IContainer $container
		 * @param IResourceManager $resourceManager
		 * @param ISchemaManager $schemaManager
		 */
		public function __construct(IContainer $container, IResourceManager $resourceManager, ISchemaManager $schemaManager) {
			$this->container = $container;
			$this->resourceManager = $resourceManager;
			$this->schemaManager = $schemaManager;
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

		public function getSchema($name) {
			if (isset($this->schemaList[$name]) === false) {
				throw new TemplateException(sprintf('Unknown schema [%s]; did you used "schema" node?', $name));
			}
			return $this->schemaList[$name];
		}

		protected function nodeTemplate(INode $root, IControl $control) {
			foreach ($root->getNodeList() as $node) {
				$this->control($node, $control);
			}
		}

		protected function nodeUse(INode $root) {
			$this->nodeList[$root->getAttribute('name')] = function (INode $node, IControl $parent) use ($root) {
				/** @var $control IControl */
				$parent->addControl($control = $this->container->create($root->getAttribute('control')));
				$controlNode = $control->getNode();
				$metaList = $controlNode->getMetaList();
				$controlNode->addMetaList($node->getMetaList());
				$attributes = [];
				foreach ($node->getAttributeList() as $name => $value) {
					if (isset($this->filterList[$name]) && ($value = $this->filterList[$name]->filter($value, $control, $node)) === false) {
						continue;
					}
					$attributes[$name] = $value;
				}
				/**
				 * it's important to keep the original values which cannot be changed
				 */
				$controlNode->setAttributeList(array_merge_recursive($controlNode->getAttributeList(), $attributes));
				$controlNode->addMetaList($metaList);
				foreach ($node->getNodeList() as $child) {
					$this->control($child, $control);
				}
			};
		}

		protected function nodeSchema(INode $root) {
			$this->schemaList[$root->getAttribute('name')] = $this->schemaManager->getSchema($root->getAttribute('schema'));
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
				'property' => new PropertyAttributeFilter($this),
				'bind' => new BindAttributeFilter(),
			];
		}
	}
