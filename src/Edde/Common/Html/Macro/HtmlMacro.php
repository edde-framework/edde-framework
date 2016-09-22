<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;

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

		public function onMacro() {
			$this->write(sprintf('/** %s */', $this->macro->getPath()), 5);
			$this->write('$parent = $stack->top();', 5);
			$this->write(sprintf('$parent->addControl($control = $this->container->create(%s));', var_export($this->control, true)), 5);
			if (($value = $this->extract($this->macro, 'value')) !== null) {
				$this->write(sprintf('$control->setText(%s);', var_export($value, true)), 5);
			}
			$this->onControl($this->macro);
			$attributeList = $this->macro->getAttributeList();
			if (empty($attributeList) === false) {
				$this->write(sprintf('$control->setAttributeList(%s);', var_export($attributeList, true)), 5);
			}
			$this->write('$stack->push($control);', 5);
			$this->compile();
			$this->write('$stack->pop();', 5);
		}

		protected function onControl(INode $macro) {
		}
	}
