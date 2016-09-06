<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Template\AbstractMacro;

	class BindMacro extends AbstractMacro {
		use LazyInjectTrait;

		protected $idList;

		public function __construct() {
			parent::__construct([
				'm:id',
				'm:bind',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$this->checkValue($macro, $element);
			switch ($macro->getName()) {
				case 'm:id':
					$element->setAttribute('id', $this->idList[$macro->getValue()] = $element->getAttribute('id', $element->getMeta('control')));
					break;
				case 'm:bind':
					if (isset($this->idList[$id = $macro->getValue()]) === false) {
						throw new MacroException(sprintf('Unknown bind id [%s] at [%s].', $id, $element->getPath()));
					}
					$element->setAttribute('bind', $this->idList[$id]);
					break;
			}
			$compiler->macro($element, $element);
		}

		public function __clone() {
			$this->idList = [];
		}
	}
