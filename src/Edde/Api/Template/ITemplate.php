<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Node\INode;

	interface ITemplate {
		/**
		 * register a new macro
		 *
		 * @param IMacro $macro
		 *
		 * @return ITemplate
		 */
		public function registerMacro(IMacro $macro): ITemplate;

		/**
		 * run the given macro
		 *
		 * @param INode $root
		 * @param array $parameterList
		 *
		 * @return mixed
		 */
		public function macro(INode $root, ...$parameterList);

		/**
		 * @param string $file
		 * @param array $parameterList
		 *
		 * @return $this
		 */
		public function load(string $file, ...$parameterList);

		/**
		 * @param string $name
		 * @param mixed $value
		 *
		 * @return ITemplate
		 */
		public function setVariable(string $name, $value): ITemplate;

		/**
		 * @param array $variableList
		 *
		 * @return ITemplate
		 */
		public function setVariableList(array $variableList): ITemplate;

		/**
		 * @param string $name
		 *
		 * @return mixed
		 */
		public function getVariable(string $name);
	}
