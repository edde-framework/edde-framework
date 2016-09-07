<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Container\LazyInjectTrait;

	class BindMacro extends AbstractHtmlMacro {
		use LazyInjectTrait;

		protected $idList;

		public function __construct() {
			parent::__construct([
				'id',
				'bind',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			$destination = $compiler->getDestination();
			$this->checkValue($macro, $element);
			switch ($macro->getName()) {
				case 'id':
					$element->setAttribute('id', $this->idList[$macro->getValue()] = $element->getAttribute('id', $element->getMeta('control')));
					$this->lambda($macro, $element, $compiler);
					break;
				case 'bind':
					if (isset($this->idList[$id = $macro->getValue()]) === false) {
						throw new MacroException(sprintf('Unknown bind id [%s] at [%s].', $id, $macro->getPath()));
					}
					$element->setAttribute('bind', $this->idList[$id]);
					$this->lambda($macro, $element, $compiler);
					break;
			}
			$this->element($element, $compiler);
		}

		public function __clone() {
			$this->idList = [];
		}
	}
