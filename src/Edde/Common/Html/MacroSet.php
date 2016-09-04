<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlView;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\MacroException;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Html\Tag\ButtonControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\SpanControl;
	use Edde\Common\Html\Value\PasswordInputControl;
	use Edde\Common\Html\Value\TextInputControl;
	use Edde\Common\Node\Node;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractMacro;
	use Edde\Common\Template\Macro\Control\ControlMacro;

	/**
	 * Helper class for a html package macros.
	 */
	class MacroSet extends AbstractObject {
		static public function macroList(IContainer $container): array {
			return [
				self::controlMacro(),
				self::snippetMacro(),
				self::passMacro(),
				self::schemaMacro(),
				self::jsMacro(),
				self::cssMacro(),
				self::buttonMacro(),
				self::headerMacro(),
				self::layoutMacro(),
				$container->inject(self::bindMacro()),
				new ControlMacro('div', DivControl::class),
				new ControlMacro('span', SpanControl::class),
				new ControlMacro('text', TextInputControl::class),
				new ControlMacro('password', PasswordInputControl::class),
			];
		}

		/**
		 * root macro for all controls
		 *
		 * @return IMacro
		 */
		static public function controlMacro(): IMacro {
			return new class() extends AbstractMacro {
				public function __construct() {
					parent::__construct(['control']);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$destination = $compiler->getDestination();

					switch ($macro->getName()) {
						case 'control':
							$destination->write(sprintf("\t\tuse %s;\n", LazyInjectTrait::class));

							$dependencyList = [
								IContainer::class,
								IStyleSheetCompiler::class,
								IJavaScriptCompiler::class,
								ITemplateManager::class,
							];

							foreach ($dependencyList as $dependency) {
								$destination->write(sprintf("\t\t/** @var %s */\n\t\tprotected $%s;\n", $dependency, StringUtils::firstLower(substr(StringUtils::extract($dependency, '\\'), 1))));
							}

							$destination->write(sprintf("\t\t/** @var %s */\n", \SplStack::class));
							$destination->write("\t\tprotected \$stack;\n");
							$destination->write(sprintf("\t\t/** @var %s */\n", IHtmlView::class));
							$destination->write("\t\tprotected \$parent;\n");

							foreach ($dependencyList as $dependency) {
								$parameter = StringUtils::firstLower(substr(StringUtils::extract($dependency, '\\'), 1));
								$destination->write(sprintf("
		public function lazyt%s(%s \$%s){
			\$this->%s = \$%s;    
		}						
", StringUtils::firstUpper($parameter), $dependency, $parameter, $parameter, $parameter));
							}

							$destination->write(sprintf("
		public function __call(\$function, array \$parameterList) {
			return call_user_func_array([
				\$this->parent, 
				\$function
			], \$parameterList);
		}
					
		public function template(%s \$parent, array \$blockList = []) {
			\$this->stack = new %s();
			\$reflectionClass = new ReflectionClass(\$this->parent = \$parent);\n", IHtmlControl::class, \SplStack::class));

							if (($attributeList = $element->getAttributeList()) !== []) {
								$destination->write(sprintf("\t\t\t\$parent->setAttributeList(%s);\n", var_export($attributeList, true)));
							}
							$destination->write("\t\t\t\$this->stack->push(\$parent);\n");
							$this->element($element, $compiler);
							$destination->write("\t\t}\n");
							break;
					}
				}
			};
		}

		/**
		 * @return IMacro
		 * @throws MacroException
		 */
		static public function snippetMacro(): IMacro {
			return new class() extends AbstractMacro {
				public function __construct() {
					parent::__construct(['m:snippet']);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$destination = $compiler->getDestination();
					switch ($macro->getName()) {
						case 'm:snippet':
							$this->checkValue($macro, $element);
							$compiler->macro($element, $element);
							$destination->write("\t\t\t\$control->disconnect();\n");
							$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
							$destination->write(sprintf("\t\t\t\$parent->addControl(\$placeholder = \$this->container->create('%s'));\n", PlaceholderControl::class));
							$destination->write("\t\t\t\$placeholder->setId(\$control->getId());\n");

							$value = StringUtils::firstLower(StringUtils::camelize($macro->getValue()));
							if (strrpos($value, '()') !== false) {
								$destination->write(sprintf("\t\t\t\$this->parent->snippet(\$control, [\$this->parent, '%s']);\n", str_replace('()', '', $value)));
								break;
							}
							$destination->write("\t\t\t\$this->parent->snippet(\$control);\n");
							$destination->write(sprintf("\t\t\t\$reflectionProperty = \$reflectionClass->getProperty('%s');\n", $value));
							$destination->write("\t\t\t\$reflectionProperty->setAccessible(true);\n");
							$destination->write("\t\t\t\$reflectionProperty->setValue(\$this->parent, \$control);\n");
							break;
					}
				}
			};
		}

		static public function passMacro(): IMacro {
			return new class extends AbstractMacro {
				public function __construct() {
					parent::__construct([
						'm:pass',
						'm:pass-child',
					]);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$destination = $compiler->getDestination();
					$this->checkValue($macro, $element);
					$value = $macro->getValue();
					switch ($macro->getName()) {
						case 'm:pass':
							$compiler->macro($element, $element);
							$value = StringUtils::firstLower(StringUtils::camelize($value));
							if (strrpos($value, '()') !== false) {
								$destination->write(sprintf("\t\t\t\$this->%s(\$control);\n", str_replace('()', '', $value)));
								break;
							}
							$destination->write(sprintf("\t\t\t\$reflectionProperty = \$reflectionClass->getProperty('%s');\n", $value));
							$destination->write("\t\t\t\$reflectionProperty->setAccessible(true);\n");
							$destination->write("\t\t\t\$reflectionProperty->setValue(\$this->parent, \$control);\n");
							break;
						case 'm:pass-child':
							foreach ($element->getNodeList() as $node) {
								$node->setAttribute('m:pass', $macro->getValue());
							}
							$compiler->macro($element, $element);
							break;
					}
				}
			};
		}

		static public function schemaMacro(): IMacro {
			return new class extends AbstractMacro {
				/**
				 * @var array
				 */
				protected $schemaList = [];

				public function __construct() {
					parent::__construct([
						'schema',
						'm:schema',
					]);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					switch ($macro->getName()) {
						case 'schema':
							$this->checkLeaf($macro, $element);
							$this->checkAttribute($macro, $element, 'name', 'schema');
							$this->schemaList[$macro->getAttribute('name')] = $macro->getAttribute('schema');
							break;
						case 'm:schema':
							$this->checkValue($macro, $element);
							list($schema, $property) = explode('.', $macro->getValue());
							if (isset($this->schemaList[$schema]) === false) {
								throw new MacroException(sprintf('Unknown attribute schema [%s] on [%s].', $schema, $element->getPath()));
							}
							$element->setAttribute('data-schema', $this->schemaList[$schema]);
							$element->setAttribute('data-property', $property);
							$compiler->macro($element, $element);
							break;
					}
				}

				public function __clone() {
					$this->schemaList = [];
				}
			};
		}

		static public function jsMacro(): IMacro {
			return new class extends AbstractMacro {
				public function __construct() {
					parent::__construct([
						'js',
					]);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$this->checkLeaf($macro, $element);
					$destination = $compiler->getDestination();
					switch ($macro->getName()) {
						case 'js':
							$this->checkAttribute($macro, $element, 'src');
							$destination->write(sprintf("\t\t\t\$this->javaScriptCompiler->addFile(%s);\n", $compiler->delimite($macro->getAttribute('src'))));
							break;
					}
				}
			};
		}

		static public function cssMacro(): IMacro {
			return new class extends AbstractMacro {
				public function __construct() {
					parent::__construct([
						'css',
					]);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$this->checkLeaf($macro, $element);
					$destination = $compiler->getDestination();
					switch ($macro->getName()) {
						case 'css':
							$this->checkAttribute($macro, $element, 'src');
							$destination->write(sprintf("\t\t\t\$this->styleSheetCompiler->addFile(%s);\n", $compiler->delimite($macro->getAttribute('src'))));
							break;
					}
				}
			};
		}

		static public function buttonMacro(): IMacro {
			return new class extends ControlMacro {
				public function __construct() {
					parent::__construct(['button'], ButtonControl::class);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$this->checkLeaf($macro, $element);
					$destination = $compiler->getDestination();
					switch ($macro->getName()) {
						case 'button':
							$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
							$destination->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", $this->control));
							$attributeList = $this->getAttributeList($macro, $compiler);
							if (isset($attributeList['action']) === false) {
								throw new MacroException(sprintf('Missing mandatory attribute "action" in [%s].', $macro->getPath()));
							}
							if (strrpos($action = $macro->getAttribute('action'), '()', 0) === false) {
								throw new MacroException(sprintf('Action [%s] attribute needs to have () at the end.', $action));
							}
							$action = str_replace('()', '', $action);
							unset($attributeList['action']);
							$destination->write(sprintf("\t\t\t\$control->setAction([\$this->parent, %s]);\n", $compiler->delimite($action)));
							$this->writeAttributeList($attributeList, $destination);
							break;
					}
				}
			};
		}

		static public function headerMacro(): IMacro {
			return new class extends ControlMacro {
				public function __construct() {
					parent::__construct([
						'h1',
						'h2',
						'h3',
						'h4',
						'h5',
						'h6',
					], '');
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$destination = $compiler->getDestination();
					$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
					$destination->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", HeaderControl::class));
					$destination->write(sprintf("\t\t\t\$control->setTag('%s');\n", $element->getName()));
					$this->writeTextValue($element, $destination, $compiler);
					$this->writeAttributeList($this->getAttributeList($element, $compiler), $destination);
					$this->element($element, $compiler);
				}
			};
		}

		static public function layoutMacro(): IMacro {
			return new class extends AbstractMacro {
				protected $layout;

				public function __construct() {
					parent::__construct([
						'm:layout',
						'n:layout',
						'block',
						'm:block',
					]);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$destination = $compiler->getDestination();
					switch ($macro->getName()) {
						case 'm:layout':
							$this->checkValue($macro, $element);
							$src = $macro->getValue();
							if ($this->layout !== null) {
								throw new MacroException(sprintf('Cannot use layout [%s]; layout was already set to [%s].', $src, $this->layout));
							}
							$element->setNodeList(array_merge($element->getNodeList(), [new Node('n:layout', $src)]));
							$compiler->macro($element, $element);
							break;
						case 'n:layout':
							$this->checkValue($macro, $element);
							$destination->write(sprintf("\t\t\t\$template = \$this->templateManager->template(%s);\n", $compiler->delimite($macro->getValue())));
							$destination->write("\t\t\t\$template->getInstance(\$this->container)->template(\$this->parent, \$blockList ?? []);\n");
							break;
						/**
						 * block placeholder generator
						 */
						case 'block':
							$this->checkAttribute($macro, $element, 'name');
							$destination->write(sprintf("\t\t\tcall_user_func(\$blockList[%s], \$control);\n", $compiler->delimite($macro->getAttribute('name'))));
							break;
						/**
						 * block reference
						 */
						case 'm:block':
							$this->checkValue($macro, $element);
							$destination->write("\t\t\t\$blockList = \$blockList ?? [];\n");
							$destination->write(sprintf("\t\t\t\$blockList[%s] = function(\$parent) {\n", $compiler->delimite($macro->getValue())));
							$destination->write("\t\t\t\$this->stack = new SplStack();\n");
							$destination->write("\t\t\t\$this->stack->push(\$parent);\n");
							$compiler->macro($element, $element);
							$destination->write("\t\t\t\$this->stack->pop();\n");
							$destination->write("\t\t\treturn \$control;\n");
							$destination->write("\t\t\t};\n");
							break;
					}
				}
			};
		}

		static public function bindMacro(): IMacro {
			return new class extends AbstractMacro {
				use LazyInjectTrait;

				protected $idList;
				/**
				 * @var ICryptEngine
				 */
				protected $cryptEngine;

				public function __construct() {
					parent::__construct([
						'm:id',
						'm:bind',
					]);
				}

				public function lazyCryptEngine(ICryptEngine $cryptEngine) {
					$this->cryptEngine = $cryptEngine;
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$this->checkValue($macro, $element);
					switch ($macro->getName()) {
						case 'm:id':
							$element->setAttribute('id', $this->idList[$macro->getValue()] = $element->getAttribute('id', $this->cryptEngine->guid()));
							break;
						case 'm:bind':
							if (isset($this->idList[$id = $macro->getValue()]) === false) {
								throw new MacroException(sprintf('Unknown bind id [%s] at [%s].', $id, $element->getPath()));
							}
							$element->setAttribute('bind', $this->idList[$id]);
							break;
					}
					$compiler->macro($element, $element);
				}

				public function __clone() {
					$this->idList = [];
				}
			};
		}
	}
