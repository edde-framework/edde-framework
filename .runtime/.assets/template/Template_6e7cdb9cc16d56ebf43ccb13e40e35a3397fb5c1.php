<?php
	declare(strict_types = 1);

	/**
	 * @generated at 2017-01-11 10:30:29
	 * automagically generated template file from the following source list:
	 *   - C:/Users/Marek Hanzal/projects/edde-framework/edde-framework-3.0/.runtime/src/Edde/App/template/layout.xml
	 */
	class Template_6e7cdb9cc16d56ebf43ccb13e40e35a3397fb5c1 extends Edde\Common\Html\AbstractHtmlTemplate {
		public function snippet(Edde\Api\Html\IHtmlControl $root, string $snippet = null): Edde\Api\Html\IHtmlControl {
			$this->embed($this);
			$stack = new SplStack();
			$stack->push($control = $parent = $root);
			switch ($snippet) {
				case null:
					$control->setAttributeList(['title' => 'title']);
					/** /control/h1 */
					$parent = $stack->top();
					$control = $this->container->create('Edde\\Common\\Html\\HeaderControl');
					$control->setText('It seems that it\'s working once again!');
					$control->setTag('h1');
					$parent->addControl($control);
					$stack->push($control);
					$stack->pop();
					/** /control/div */
					$parent = $stack->top();
					$control = $this->container->create('Edde\\Common\\Html\\Tag\\DivControl');
					$control->setText('hello there!');
					$parent->addControl($control);
					$stack->push($control);
					$stack->pop();
					break;
				default:
					throw new Edde\Api\TemplateEngine\TemplateException(sprintf('Requested unknown snippet [%s].', $snippet));
			}
			return $root;
		}

		public function getBlockList(): array {
			return array (
);
		}
	}
