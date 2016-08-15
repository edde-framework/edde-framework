<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template\Macro;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ITemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Template\AbstractMacro;

	class JsNodeMacro extends AbstractMacro {
		public function __construct() {
			parent::__construct(['js']);
		}

		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IFile $file, ...$parameterList) {
			$templateFile = $template->getFile();
			$templateFile->write(sprintf("\t\t\t\$this->javaScriptCompiler->addFile('%s');\n", $file->getDirectory()
				->filename($root->getAttribute('src'))));
		}
	}
