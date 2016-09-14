<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\File\IFile;
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
			switch ($macro->getName()) {
				case 'pass':
					$this->start($macro, $element, $compiler);
					$this->dependencies($macro, $compiler);
					$this->pass($macro, $destination);
					$this->end($macro, $element, $compiler);
					break;
				case 'pass-child':
					$this->start($macro, $element, $compiler);
					$this->dependencies($macro, $compiler);
					$destination->write("\t\t\t\tforeach(\$last->getControlList() as \$last) {\n");
					$this->pass($macro, $destination);
					$destination->write("\t\t\t\t}\n");
					$this->end($macro, $element, $compiler);
					break;
			}
		}

		protected function pass(INode $macro, IFile $destination) {
			$value = $macro->getValue();
			if (strrpos($value = StringUtils::firstLower(StringUtils::camelize($value)), '()') !== false) {
				$destination->write(sprintf("\t\t\t\t\$this->%s(\$last);\n", str_replace('()', '', $value)));
			} else {
				$destination->write(sprintf("\t\t\t\t\$reflectionProperty = \$this->reflectionClass->getProperty('%s');\n", $value));
				$destination->write("\t\t\t\t\$reflectionProperty->setAccessible(true);\n");
				$destination->write("\t\t\t\t\$reflectionProperty->setValue(\$this->root, \$last);\n");
			}
		}
	}
