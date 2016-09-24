<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\File\IFile;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Resource\IResourceList;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Url\IUrl;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Template\AbstractTemplate;

	/**
	 * Abstract helper class fro all html based templates; this should be used only by a template generator.
	 */
	abstract class AbstractHtmlTemplate extends AbstractTemplate implements IHtmlTemplate, ILazyInject {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;
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
			/** @noinspection UnnecessaryParenthesesInspection */
			(function (IUrl $url) {
				require_once $url->getAbsoluteUrl();
			})($file->getUrl());
			$class = str_replace('.php', '', $file->getName());
			return $container->inject(new $class());
		}

		/**
		 * @param IContainer $container
		 */
		public function lazyContainer(IContainer $container) {
			$this->container = $container;
		}

		/**
		 * @param ITemplateManager $templateManager
		 */
		public function lazyTemplateManager(ITemplateManager $templateManager) {
			$this->templateManager = $templateManager;
		}

		/**
		 * @param IStyleSheetCompiler $styleSheetList
		 */
		public function lazyStyleSheetList(IStyleSheetCompiler $styleSheetList) {
			$this->styleSheetList = $styleSheetList;
		}

		/**
		 * @param IJavaScriptCompiler $javaScriptList
		 */
		public function lazyJavaScriptList(IJavaScriptCompiler $javaScriptList) {
			$this->javaScriptList = $javaScriptList;
		}
	}
