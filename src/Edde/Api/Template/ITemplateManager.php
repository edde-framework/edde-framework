<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\File\IFile;

	interface ITemplateManager extends IConfigurable {
		/**
		 * @param array $nameList
		 *
		 * @return ITemplateManager
		 */
		public function compile(array $nameList): ITemplateManager;

		/**
		 * build a template from the given (already registered) snippets
		 *
		 * @param string      $name
		 * @param mixed|null  $context
		 * @param string|null $namespace
		 * @param array       $parameterList
		 *
		 * @return
		 */
		public function template(string $name, $context = null, string $namespace = null, ...$parameterList);

		/**
		 * compile only the given snippet
		 *
		 * @param string      $name          snippet name (could be passed to resource provider
		 * @param string|null $namespace     basically for resource provider compatibility
		 * @param array       $parameterList resource provider compatibility
		 *
		 * @return IFile
		 */
		public function snippet(string $name, string $namespace = null, ...$parameterList): IFile;
	}
