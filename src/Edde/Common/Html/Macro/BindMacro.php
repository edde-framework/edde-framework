<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Container\LazyInjectTrait;

	class BindMacro extends AbstractHtmlMacro {
		use LazyInjectTrait;

		protected $bindList;

		public function __construct() {
			parent::__construct([
				'id',
				'bind',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$this->checkValue($macro, $element);
			switch ($macro->getName()) {
				case 'id':
					$element->setAttribute('id', $this->bindList[$macro->getValue()] = $element->getAttribute('id', $element->getMeta('control')));
					$this->start($macro, $element, $compiler);
					$this->dependencies($macro, $compiler);
					$this->end($macro, $element, $compiler);
					break;
				case 'bind':
					if (isset($this->bindList[$id = $macro->getValue()]) === false) {
						throw new MacroException(sprintf('Unknown bind id [%s] at [%s].', $id, $macro->getPath()));
					}
					$element->setAttribute('bind', $this->bindList[$id]);
					$this->start($macro, $element, $compiler);
					$this->dependencies($macro, $compiler);
					$this->end($macro, $element, $compiler);
					break;
			}
			$this->element($element, $compiler);
		}

		public function __clone() {
			$this->bindList = [];
		}
	}
