<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\ILazyInject;
	use Edde\Api\Control\IControl;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Html\IHtmlTemplate;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Api\Template\TemplateException;
	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Template\AbstractTemplate;
	use Edde\Common\Usable\UsableTrait;

	abstract class AbstractHtmlTemplate extends AbstractTemplate implements IHtmlTemplate, ILazyInject {
		use UsableTrait;
		/**
		 * @var IHtmlControl
		 */
		protected $root;
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
		 * @var string[]
		 */
		protected $importList = [];
		/**
		 * @var \ReflectionClass
		 */
		protected $reflectionClass;
		/**
		 * inter-lambda communication
		 *
		 * @var array
		 */
		protected $stash;
		/**
		 * @var IControl
		 */
		protected $last;
		/**
		 * @var callable[]
		 */
		protected $controlList;

		/**
		 * @param IHtmlControl $root
		 */
		public function __construct(IHtmlControl $root) {
			$this->root = $root;
		}

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
				$this->root,
				$function,
			], $parameterList);
		}

		public function import(...$importList): IHtmlTemplate {
			$this->importList = array_merge($this->importList, $importList);
			return $this;
		}

		public function template(): IHtmlTemplate {
			$this->use();
			$this->getControlList()[null]($this->root);
			return $this;
		}

		public function getControlList(): array {
			$this->use();
			return $this->controlList;
		}

		public function snippet(string $name, IControl $root): IControl {
			$this->use();
			if (isset($this->controlList[$name]) === false) {
				throw new TemplateException(sprintf('Requested unknown control block [%s] on [%s].', $name, $root->getNode()
					->getPath()));
			}
			return $this->controlList[$name]($root);
		}

		protected function prepare() {
			$this->onPrepare();
			$this->reflectionClass = new \ReflectionClass($this->root);
			$callback = $this->getControlList()[null];
			foreach (array_unique($this->importList) as $import) {
				/** @var $template IHtmlTemplate */
				if ((($template = $this->templateManager->template($import, $this->root)) instanceof IHtmlTemplate) === false) {
					throw new TemplateException(sprintf('Unsupported included template [%s] type [%s]; template must be instance of [%s].', $import, get_class($template), IHtmlTemplate::class));
				}
				$this->controlList = array_merge($this->controlList, $template->getControlList());
			}
			$this->controlList[null] = $callback;
		}

		abstract protected function onPrepare();
	}
