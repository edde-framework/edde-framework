<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro\Control;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\AbstractMacro;

	class PassMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct([
				'm:pass',
				'm:pass-child',
			]);
		}

		public function run(INode $root, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$value = str_replace('()', '', $root->getValue());
			switch ($root->getName()) {
				case 'm:pass':
					$this->macro($root, $compiler);
					$destination->write(sprintf("\t\t\t\$this->%s(\$control);\n", StringUtils::firstLower(StringUtils::camelize($value))));
					break;
				case 'm:pass-child':
					foreach ($root->getNodeList() as $node) {
						$compiler->macro($node, $compiler, function (ICompiler $compiler) use ($value) {
							$destination = $compiler->getDestination();
							$destination->write(sprintf("\t\t\t\$this->%s(\$control);\n", StringUtils::firstLower(StringUtils::camelize($value))));
						});
					}
					break;
			}
		}
	}
