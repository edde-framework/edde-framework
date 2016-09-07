<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Strings\StringUtils;

	class PassMacro extends AbstractHtmlMacro {
		public function __construct() {
			parent::__construct([
				'pass',
				'pass-child',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$this->checkValue($macro, $element);
			$value = $macro->getValue();
			switch ($macro->getName()) {
				case 'pass':
					$compiler->macro($element, $element);
					$value = StringUtils::firstLower(StringUtils::camelize($value));
					$this->start($macro, $element, $compiler);
					foreach ($macro->getNodeList() as $node) {
						$destination->write(sprintf("\t\t\t\t/** %s */\n", $node->getPath()));
						$destination->write(sprintf("\t\t\t\t\$controlList[%s](\$control);\n", $compiler->delimite($node->getMeta('control'))));
					}
					if (strrpos($value, '()') !== false) {
						$destination->write(sprintf("\t\t\t\t\$this->%s(\$control);\n", str_replace('()', '', $value)));
					} else {
						$destination->write(sprintf("\t\t\t\$reflectionProperty = \$reflectionClass->getProperty('%s');\n", $value));
						$destination->write("\t\t\t\$reflectionProperty->setAccessible(true);\n");
						$destination->write("\t\t\t\$reflectionProperty->setValue(\$this->root, \$control);\n");
					}
					$this->end($macro, $element, $compiler);
					$this->element($macro, $compiler);
					break;
				case 'pass-child':
					foreach ($element->getNodeList() as $node) {
						$node->setAttribute('m:pass', $macro->getValue());
					}
					$compiler->macro($element, $element);
					break;
			}
		}
	}
