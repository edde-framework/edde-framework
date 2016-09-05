<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\TemplateException;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\AbstractObject;
	use Edde\Common\Container\LazyInjectTrait;
	use SplStack;

	abstract class HtmlTemplate extends AbstractObject implements IHtmlTemplate {
		use LazyInjectTrait;

		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IStyleSheetCompiler
		 */
		protected $styleSheetCompiler;
		/**
		 * @var IJavaScriptCompiler
		 */
		protected $javaScriptCompiler;
		/**
		 * @var ITemplateManager
		 */
		protected $templateManager;
		/**
		 * @var SplStack
		 */
		protected $stack;
		/**
		 * @var \Edde\Api\Html\IHtmlView
		 */
		protected $parent;
		protected $blockList = [];

		public function lazytContainer(IContainer $container) {
			$this->container = $container;
		}

		public function lazytStyleSheetCompiler(IStyleSheetCompiler $styleSheetCompiler) {
			$this->styleSheetCompiler = $styleSheetCompiler;
		}

		public function lazytJavaScriptCompiler(IJavaScriptCompiler $javaScriptCompiler) {
			$this->javaScriptCompiler = $javaScriptCompiler;
		}

		public function lazytTemplateManager(ITemplateManager $templateManager) {
			$this->templateManager = $templateManager;
		}

		public function __call($function, array $parameterList) {
			return call_user_func_array([
				$this->parent,
				$function,
			], $parameterList);
		}

		public function getBlockList(): array {
			return $this->blockList;
		}

		public function block(string $name, IHtmlControl $parent) {
			if (isset($this->blockList[$name]) === false) {
				throw new TemplateException(sprintf('Requested unknown block [%s].', $name));
			}
			call_user_func($this->blockList[$name], $parent);
		}

		protected function use (string $file) {
			$template = $this->templateManager->template($file);
			$template = $template->getInstance($this->container);
			$template->template($container = new ContainerControl());
			if ($container->isLeaf() === false) {
				throw new TemplateException(sprintf('Template [%s] can contain only block controls.', $file));
			}
			$this->blockList = array_merge($this->blockList, $template->getBlockList());
		}
	}