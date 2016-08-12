<?php
	declare(strict_types = 1);

	namespace Edde\Api\Template;

	use Edde\Api\Container\IContainer;
	use Edde\Api\File\IFile;

	interface ITemplate {
		/**
		 * @return IFile
		 */
		public function getFile(): IFile;

		/**
		 * @return string
		 */
		public function getClass(): string;

		/**
		 * if the container is not provided, instance is created "by hand"
		 *
		 * @param IContainer $container
		 *
		 * @return mixed
		 */
		public function getInstance(IContainer $container = null);
	}
