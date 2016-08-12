<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;

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
		 * @param IResource $resource
		 * @param array $parameterList
		 *
		 * @return mixed
		 */
		public function run(ITemplateManager $templateManager, ITemplate $template, INode $root, IResource $resource, ...$parameterList);
	}
