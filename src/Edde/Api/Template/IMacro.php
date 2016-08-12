<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Node\INode;

	interface IMacro {
		/**
		 * return list of supported macro names (node names)
		 *
		 * @return array
		 */
		public function getMacroList(): array;

		/**
		 * @param INode $root current node to process
		 * @param array $parameterList
		 *
		 * @return mixed
		 */
		public function run(INode $root, ...$parameterList);
	}
