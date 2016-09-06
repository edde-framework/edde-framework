<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Control\IControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\MacroException;
	use Edde\Common\AbstractObject;
	use Edde\Common\Html\ContainerControl;
	use Edde\Common\Html\HeaderControl;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractMacro;

	/**
	 * Helper class for a html package macros.
	 */
	class MacroSet extends AbstractObject {
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

							$destination->write(sprintf("\t\t\t\$this->root->addSnippet(%s, function(%s \$parent) use(\$reflectionClass) {\n", $snippetId = $compiler->delimite($id = $element->getAttribute('id', sha1(random_bytes(64)))), IControl::class));
//							$destination->write(sprintf("\t\t\t\$this->stack = new %s();\n", \SplStack::class));
							$destination->write("\t\t\t\$this->stack->push(\$parent);\n");
							$compiler->macro($element, $element);
							if ($isMethod === false) {
								$destination->write(sprintf("\t\t\t\$reflectionProperty = \$reflectionClass->getProperty('%s');\n", $value));
								$destination->write("\t\t\t\$reflectionProperty->setAccessible(true);\n");
								$destination->write("\t\t\t\$reflectionProperty->setValue(\$this->root, \$control);\n");
							}
							$destination->write("\t\t\treturn \$control;\n");
							$destination->write(sprintf("\t\t\t}%s);\n", $isMethod ? ', [$this->parent, ' . $compiler->delimite(str_replace('()', '', $value)) . ']' : ''));
							$destination->write(sprintf("\t\t\t\$this->root->snippet(%s, new %s());\n", $snippetId, ContainerControl::class));
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
							$destination->write("\t\t\t\$reflectionProperty->setValue(\$this->root, \$control);\n");
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
					], HeaderControl::class);
				}

				public function macro(INode $macro, INode $element, ICompiler $compiler) {
					$destination = $compiler->getDestination();
					$destination->write("\t\t\t\$parent = \$stack->top();\n");
					$destination->write(sprintf("\t\t\t\t\$parent->addControl(\$control = \$this->container->create(%s));\n", $compiler->delimite($this->control)));
					$destination->write(sprintf("\t\t\t\$control->setTag('%s');\n", $element->getName()));
					$this->writeTextValue($element, $destination, $compiler);
					$this->writeAttributeList($this->getAttributeList($element, $compiler), $destination);
					$this->element($element, $compiler);
				}
			};
		}

		static public function layoutMacro(): IMacro {
			return new class extends AbstractMacro {
				protected $defineList = [];

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
							$this->checkNotLeaf($macro, $element);
							$this->defineList[$macro->getAttribute('name')] = $element->getNodeList();
							break;
						case 'm:define':
							$this->checkValue($macro, $element);
							$this->defineList[$macro->getValue()] = $element;
							break;
						case 'block':
							$this->checkLeaf($macro, $element);
							$this->checkAttribute($macro, $element, 'name');
//							$destination->write(sprintf("\t\t\t\$this->block(%s, \$this->stack->top());\n", $compiler->delimite($macro->getAttribute('name'))));
							break;
						case 'm:block':
							$this->checkValue($macro, $element);
							$this->checkLeaf($macro, $element);
//							$element->addNode(new Node('block', [], ['name' => $macro->getValue()]));
//							$compiler->macro($element, $element);
							break;
					}
				}
			};
		}
	}
