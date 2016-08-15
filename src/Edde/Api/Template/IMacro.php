<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;

	interface IMacro {
		/**
		 * return list of supported macro names (node names)
		 *
		 * @return array
		 */
		public function getMacroList(): array;

		/**
		 * @param ITemplateManager $templateManager
		 * @param ITemplate $template access to current template
		 * @param INode $root current node to process
		 * @param IFile $file
		 * @param array $parameterList
		 *
		 * @return IMacro
		 */
		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IFile $file, ...$parameterList);
	}
