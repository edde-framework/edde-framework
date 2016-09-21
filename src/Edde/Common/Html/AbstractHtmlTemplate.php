<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Common\Template\AbstractTemplate;

	abstract class AbstractHtmlTemplate extends AbstractTemplate implements IHtmlTemplate, ILazyInject {
		/**
		 * @var IContainer
		 */
		protected $container;

		/**
		 * @param IFile $file
		 * @param IContainer $container
		 *
		 * @return IHtmlTemplate
		 */
		static public function template(IFile $file, IContainer $container): IHtmlTemplate {
			(function (IFile $file) {
				require_once($file->getUrl()
					->getAbsoluteUrl());
			})($file);
			$class = str_replace('.php', '', $file->getName());
			return $container->inject(new $class());
		}

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}
	}
