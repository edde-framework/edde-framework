<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Html\HeaderControl;

	/**
	 * Classic html header macro (h1, h2, ...).
	 */
	class HeaderMacro extends HtmlMacro {
		public function __construct(string $header) {
			parent::__construct($header, HeaderControl::class);
		}

		protected function onControl(INode $macro, ICompiler $compiler) {
			$this->write($compiler, sprintf('$control->setTag(%s);', var_export($this->getName(), true)), 5);
		}
	}
