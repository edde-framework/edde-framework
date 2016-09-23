<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Template\AbstractTemplate;

	abstract class AbstractHtmlTemplate extends AbstractTemplate implements IHtmlTemplate, ILazyInject {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IResourceList
		 */
		protected $styleSheetList;
		/**
		 * @var IResourceList
		 */
		protected $javaScriptList;

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

		public function lazyStyleSheetList(IStyleSheetCompiler $styleSheetList) {
			$this->styleSheetList = $styleSheetList;
		}

		public function lazyJavaScriptList(IJavaScriptCompiler $javaScriptList) {
			$this->javaScriptList = $javaScriptList;
		}

		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}
	}
