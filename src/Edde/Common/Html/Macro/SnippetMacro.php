<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;

	class SnippetMacro extends AbstractHtmlMacro {
		public function __construct() {
			parent::__construct([
				'snippet',
			]);
		}

		public function macro(INode $macro, INode $element, ICompiler $compiler) {
			switch ($macro->getName()) {
				case 'snippet':
					$macro->getMeta('inline') ? $this->checkValue($macro, $element) : $this->checkAttribute($macro, $element, 'name');
					$name = $macro->getAttribute('name', $macro->getValue());
					$this->start($macro, $element, $compiler);
					$this->end($macro, $element, $compiler, false);
					$macro->setMeta('control', $name);
					$this->lambda($macro, $element, $compiler);
					break;
			}
		}
	}
