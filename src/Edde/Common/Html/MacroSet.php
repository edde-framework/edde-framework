<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Control\IControl;
	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\MacroException;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Html\Input\PasswordControl;
	use Edde\Common\Html\Input\TextControl;
	use Edde\Common\Html\Tag\ButtonControl;
	use Edde\Common\Html\Tag\DivControl;
	use Edde\Common\Html\Tag\SpanControl;
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
				new ControlMacro('text', TextControl::class),
				new ControlMacro('password', PasswordControl::class),
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
					parent::__construct([
						'control',
					]);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$destination = $compiler->getDestination();
					$source = $compiler->getSource();

					switch ($macro->getName()) {
						case 'control':
							$destination->write("<?php\n");
							$destination->write("\tdeclare(strict_types = 1);\n\n");
							$destination->write(sprintf("\t/** source = %s */\n\n", $source->getPath()));
							$destination->write(sprintf("\tclass %s extends %s {\n", $compiler->getName(), HtmlTemplate::class));
							$destination->write(vsprintf("\t\tpublic function template(%s \$parent, array \$useList = []) {
			\$this->stack = new %s();
			\$reflectionClass = new ReflectionClass(\$this->parent = \$parent);
			foreach (\$useList as \$use) {
				\$this->use(\$use);
			}\n", [
								IHtmlControl::class,
								\SplStack::class,
							]));

							if (($attributeList = $element->getAttributeList()) !== []) {
								$destination->write(sprintf("\t\t\t\$parent->setAttributeList(%s);\n", var_export($attributeList, true)));
							}
							$destination->write("\t\t\t\$this->stack->push(\$parent);\n");
							$this->element($element, $compiler);
							$destination->write("\t\t}\n");
							$destination->write("\t}\n");
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
					parent::__construct([
						'm:snippet',
					]);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$destination = $compiler->getDestination();
					switch ($macro->getName()) {
						case 'm:snippet':
							$this->checkValue($macro, $element);
							$this->checkElementAttribute($macro, $element, 'id');

							$value = StringUtils::firstLower(StringUtils::camelize($macro->getValue()));
							$isMethod = strrpos($value, '()') !== false;

							$destination->write(sprintf("\t\t\t\$this->parent->addSnippet(%s, function(%s \$parent) use(\$reflectionClass) {\n", $snippetId = $compiler->delimite($id = $element->getAttribute('id', sha1(random_bytes(64)))), IControl::class));
							$destination->write(sprintf("\t\t\t\$this->stack = new %s();\n", \SplStack::class));
							$destination->write("\t\t\t\$this->stack->push(\$parent);\n");
							$compiler->macro($element, $element);
							if ($isMethod === false) {
								$destination->write(sprintf("\t\t\t\$reflectionProperty = \$reflectionClass->getProperty('%s');\n", $value));
								$destination->write("\t\t\t\$reflectionProperty->setAccessible(true);\n");
								$destination->write("\t\t\t\$reflectionProperty->setValue(\$this->parent, \$current);\n");
							}
							$destination->write("\t\t\treturn \$current;\n");
							$destination->write(sprintf("\t\t\t}%s);\n", $isMethod ? ', [$this->parent, ' . $compiler->delimite(str_replace('()', '', $value)) . ']' : ''));
							$destination->write(sprintf("\t\t\t\$this->parent->snippet(%s, new %s());\n", $snippetId, ContainerControl::class));
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
								$destination->write(sprintf("\t\t\t\$this->%s(\$current);\n", str_replace('()', '', $value)));
								break;
							}
							$destination->write(sprintf("\t\t\t\$reflectionProperty = \$reflectionClass->getProperty('%s');\n", $value));
							$destination->write("\t\t\t\$reflectionProperty->setAccessible(true);\n");
							$destination->write("\t\t\t\$reflectionProperty->setValue(\$this->parent, \$current);\n");
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
							$this->writeCreateControl($destination, $this->control);
							$attributeList = $this->getAttributeList($macro, $compiler);
							if (isset($attributeList['action']) === false) {
								throw new MacroException(sprintf('Missing mandatory attribute "action" in [%s].', $macro->getPath()));
							}
							if (strrpos($action = $macro->getAttribute('action'), '()', 0) === false) {
								throw new MacroException(sprintf('Action [%s] attribute needs to have () at the end.', $action));
							}
							$action = str_replace('()', '', $action);
							unset($attributeList['action']);
							$destination->write(sprintf("\t\t\t\$current->setAction([\$this->parent, %s]);\n", $compiler->delimite($action)));
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
					$this->writeCreateControl($destination, HeaderControl::class);
					$destination->write(sprintf("\t\t\t\$current->setTag('%s');\n", $element->getName()));
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
						'use',
						'define',
						'm:define',
						'block',
						'm:block',
					]);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$destination = $compiler->getDestination();
					switch ($macro->getName()) {
						case 'use':
							$this->checkAttribute($macro, $element, 'src');
							$destination->write(sprintf("\t\t\t\$this->use(%s);\n", $compiler->delimite($macro->getAttribute('src'))));
							break;
						case 'define':
							$this->checkAttribute($macro, $element, 'name');
							$destination->write(sprintf("\t\t\t\$this->blockList[%s] = function(%s \$parent) use(\$reflectionClass) {\n", $compiler->delimite($macro->getAttribute('name')), IHtmlControl::class));
							$destination->write(sprintf("\t\t\t\t\$this->stack = new %s();\n", \SplStack::class));
							$destination->write("\t\t\t\t\$this->stack->push(\$parent);\n");
							$this->element($element, $compiler);
							$destination->write("\t\t\t};\n");
							break;
						case 'm:define':
							$this->checkValue($macro, $element);
							if ($element->isRoot()) {
								$element->addNode((new Node('define', null, ['name' => $macro->getValue()]))->addNodeList($element->getNodeList(), true));
								$compiler->macro($element, $element);
								return;
							}
							$destination->write(sprintf("\t\t\t\$this->blockList[%s] = function(%s \$parent) {\n", $compiler->delimite($macro->getValue()), IHtmlControl::class));
							$destination->write(sprintf("\t\t\t\t\$this->stack = new %s();\n", \SplStack::class));
							$destination->write("\t\t\t\t\$this->stack->push(\$parent);\n");
							$compiler->macro($element, $element);
							$destination->write("\t\t\t};\n");
							break;
						case 'block':
							$this->checkLeaf($macro, $element);
							$this->checkAttribute($macro, $element, 'name');
							$destination->write(sprintf("\t\t\t\$this->block(%s, \$this->stack->top());\n", $compiler->delimite($macro->getAttribute('name'))));
							break;
						case 'm:block':
							$this->checkValue($macro, $element);
							$this->checkLeaf($macro, $element);
							$element->addNode(new Node('block', [], ['name' => $macro->getValue()]));
							$compiler->macro($element, $element);
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
