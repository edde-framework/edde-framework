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
			parent::__construct($name);
			$this->control = $control;
		}

		public function onMacro(INode $macro) {
			$this->write(sprintf('case %s:', var_export($macro->getMeta('id'), true)), 5);
			$this->write("break;\n", 5);
		}
	}
