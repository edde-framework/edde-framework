<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	/**
	 * Common stuff control macro.
	 */
	class HtmlMacro extends AbstractHtmlMacro {
		/**
		 * @var string
		 */
		protected $control;

		/**
		 * @param string $name
		 * @param string $control
		 */
		public function __construct(string $name, string $control) {
			parent::__construct($name, false);
			$this->control = $control;
		}

		/**
		 * @inheritdoc
		 */
		public function macro(INode $macro, ICompiler $compiler) {
			$this->write($compiler, sprintf('/** %s */', $macro->getPath()), 5);
			$this->write($compiler, '$parent = $stack->top();', 5);
			$this->write($compiler, sprintf('$parent->addControl($control = $this->container->create(%s));', var_export($this->control, true)), 5);
			$this->writeTextValue($macro, $compiler);
			$this->onControl($macro, $compiler);
			$this->writeAttributeList($macro, $compiler);
			$this->write($compiler, '$stack->push($control);', 5);
			foreach ($macro->getNodeList() as $node) {
				$compiler->runtimeMacro($node);
			}
			$this->write($compiler, '$stack->pop();', 5);
		}

		protected function onControl(INode $macro, ICompiler $compiler) {
		}
	}
