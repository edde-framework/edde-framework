<?php
	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Common\Html\HeaderControl;

	class HeaderMacro extends HtmlMacro {
		public function __construct(string $header) {
			parent::__construct($header, HeaderControl::class);
		}

		protected function onControl(INode $macro) {
			$this->write(sprintf('$control->setTag(%s);', var_export($this->getName(), true)), 5);
		}
	}
