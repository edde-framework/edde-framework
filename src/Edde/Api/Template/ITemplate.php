<?php
	declare(strict_types=1);

	namespace Edde\Api\Template;

	use Edde\Api\Config\IConfigurable;
	use Edde\Api\File\IFile;
	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;

	interface ITemplate extends IConfigurable {
		/**
		 * @param string $name
		 * @param IMacro $macro
		 *
		 * @return ITemplate
		 */
		public function registerMacro(string $name, IMacro $macro): ITemplate;

		/**
		 * from where this template can got another templates?
		 *
		 * @param ITemplateProvider $templateProvider
		 *
		 * @return ITemplate
		 */
		public function registerTemplateProvider(ITemplateProvider $templateProvider): ITemplate;

		/**
		 * add list of resources of this template
		 *
		 * @param string    $name
		 * @param IResource $resource
		 *
		 * @return ITemplate
		 */
		public function import(string $name, IResource $resource): ITemplate;

		/**
		 * execute template compilation
		 */
		public function compile();

		/**
		 * register the given block to the template; the block should NOT be modified by template
		 *
		 * @param string $name
		 * @param INode  $node
		 *
		 * @return ITemplate
		 */
		public function block(string $name, INode $node): ITemplate;

		/**
		 * retrieve block with the given name; node is current macro; returned block should NOT be modified (or cloned) by template
		 *
		 * @param string $name
		 * @param INode  $node
		 *
		 * @return INode
		 */
		public function getBlock(string $name, INode $node): INode;

		/**
		 * return template's source file (output)
		 *
		 * @return IFile
		 */
		public function getFile(): IFile;
	}
