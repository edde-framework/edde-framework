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

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					$destination = $compiler->getDestination();
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

					if (($attributeList = $root->getAttributeList()) !== []) {
						$destination->write(sprintf("\t\t\t\$parent->setAttributeList(%s);\n", var_export($attributeList, true)));
					}
					$destination->write("\t\t\t\$this->stack->push(\$parent);\n");
					$this->macro($root, $compiler, $callback);
					$destination->write("\t\t}\n");
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

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					$destination = $compiler->getDestination();
					switch ($root->getName()) {
						case 'm:snippet':
							$this->macro($root, $compiler, $callback);
							$destination->write("\t\t\t\$control->disconnect();\n");
							$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
							$destination->write(sprintf("\t\t\t\$parent->addControl(\$placeholder = \$this->container->create('%s'));\n", PlaceholderControl::class));
							$destination->write("\t\t\t\$placeholder->setId(\$control->getId());\n");

							$value = StringUtils::firstLower(StringUtils::camelize($root->getValue()));
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

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					$destination = $compiler->getDestination();
					$value = $root->getValue();
					switch ($root->getName()) {
						case 'm:pass':
							$this->macro($root, $compiler, $callback);
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
							$value = str_replace('()', '', $root->getValue());
							foreach ($root->getNodeList() as $node) {
								$compiler->macro($node, $compiler, function (ICompiler $compiler) use ($value) {
									$destination = $compiler->getDestination();
									$destination->write(sprintf("\t\t\t\$this->%s(\$control);\n", StringUtils::firstLower(StringUtils::camelize($value))));
								});
							}
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

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					switch ($root->getName()) {
						case 'schema':
							$this->schemaList[$root->getAttribute('name')] = $root->getAttribute('schema');
							break;
						case 'm:schema':
							$attribute = explode('.', $root->getValue());
							if (isset($this->schemaList[$attribute[0]]) === false) {
								throw new MacroException(sprintf('Unknown attribute schema [%s] on [%s].', $attribute[0], $root->getPath()));
							}
							$node = $root->getNodeList()[0];
							$node->setAttribute('data-schema', $this->schemaList[$attribute[0]]);
							$node->setAttribute('data-property', $attribute[1]);
							$this->macro($root, $compiler, $callback);
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
						'e:js',
					]);
				}

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					$destination = $compiler->getDestination();
					$file = null;
					switch ($root->getName()) {
						case 'js':
							$file = $compiler->file($root->getAttribute('src'));
							break;
						case 'e:js':
							$file = $compiler->asset($root->getAttribute('src'));
							break;
					}
					$destination->write(sprintf("\t\t\t\$this->javaScriptCompiler->addFile('%s');\n", $file));
				}
			};
		}

		static public function cssMacro(): IMacro {
			return new class extends AbstractMacro {
				public function __construct() {
					parent::__construct([
						'css',
						'e:css',
					]);
				}

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					$destination = $compiler->getDestination();
					$file = null;
					switch ($root->getName()) {
						case 'css':
							$file = $compiler->file($root->getAttribute('src'));
							break;
						case 'e:css':
							$file = $compiler->asset($root->getAttribute('src'));
							break;
					}
					$destination->write(sprintf("\t\t\t\$this->styleSheetCompiler->addFile('%s');\n", $file));
				}
			};
		}

		static public function buttonMacro(): IMacro {
			return new class extends ControlMacro {
				public function __construct() {
					parent::__construct(['button'], ButtonControl::class);
				}

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					$destination = $compiler->getDestination();
					$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
					$destination->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", $this->control));
					$attributeList = $this->getAttributeList($root, $compiler);
					if (isset($attributeList['action']) === false) {
						throw new MacroException(sprintf('Missing mandatory attribute "action" in [%s].', $root->getPath()));
					}
					if (strrpos($action = $root->getAttribute('action'), '()', 0) === false) {
						throw new MacroException(sprintf('Action [%s] attribute needs to have () at the end.', $action));
					}
					$action = str_replace('()', '', $action);
					unset($attributeList['action']);
					$destination->write(sprintf("\t\t\t\$control->setAction([\$this->parent, %s]);\n", $compiler->value($action)));
					$this->writeAttributeList($attributeList, $destination);
					$this->macro($root, $compiler, $callback);
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

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					$destination = $compiler->getDestination();
					$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
					$destination->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", HeaderControl::class));
					$destination->write(sprintf("\t\t\t\$control->setTag('%s');\n", $root->getName()));
					$this->writeTextValue($root, $destination, $compiler);
					$this->writeAttributeList($this->getAttributeList($root, $compiler), $destination);
					$this->macro($root, $compiler, $callback);
				}
			};
		}

		static public function layoutMacro(): IMacro {
			return new class extends AbstractMacro {
				protected $layout;

				public function __construct() {
					parent::__construct([
						'm:layout',
						'block',
						'm:block',
					]);
				}

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					$destination = $compiler->getDestination();
					switch ($root->getName()) {
						case 'm:layout':
							if (($src = $root->getValue()) === null) {
								throw new MacroException(sprintf('Missing attribute of macro [%s].', $root->getName()));
							}
							if ($this->layout !== null) {
								throw new MacroException(sprintf('Cannot use layout [%s]; layout was already set to [%s].', $src, $this->layout));
							}
							$destination->write(sprintf("\t\t\t\$this->templateManager->template(%s);\n", $compiler->value($src)));
							break;
						/**
						 * block placeholder generator
						 */
						case 'block':
							break;
						/**
						 * block reference
						 */
						case 'm:block':
							$destination->write("\t\t\t// block start here\n");
							$this->macro($root, $compiler, $callback);
							$destination->write("\t\t\t// block end here\n");
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

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					if ($root->isLeaf()) {
						throw new MacroException(sprintf('Node [%s] must have children.', $root->getPath()));
					}
					$node = $root->getNodeList()[0];
					switch ($root->getName()) {
						case 'm:id':
							$node->setAttribute('id', $this->idList[$root->getValue()] = $node->getAttribute('id', $this->cryptEngine->guid()));
							break;
						case 'm:bind':
							if (isset($this->idList[$id = $root->getValue()]) === false) {
								throw new MacroException(sprintf('Unknown bind id [%s].', $id));
							}
							$node->setAttribute('bind', $this->idList[$id]);
							break;
					}
					$this->macro($root, $compiler, $callback);
				}

				public function __clone() {
					$this->idList = [];
				}
			};
		}
	}
