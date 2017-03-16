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
		 * @param string     $name
		 * @param mixed|null $context
		 */
		public function template(string $name, $context = null);

		/**
		 * compile only the given snippet
		 *
		 * @param string $name
		 *
		 * @return IFile
		 */
		public function snippet(string $name): IFile;
	}
