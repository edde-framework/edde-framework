<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Common\Html\Tag\ButtonControl;

	class ButtonMacro extends HtmlMacro {
		public function __construct() {
			parent::__construct('button', ButtonControl::class);
		}

		protected function onControl(INode $macro) {
			if (($action = $this->extract($macro, 'm:action')) !== null) {
				$this->write(sprintf('$control->setAction(%s);', $this->action($action)), 5);
			}
		}

		protected function action(string $action) {
			if ($action[0] !== '@' && substr($action, -2) === '()') {
				return sprintf('[$root, %s]', var_export(str_replace('()', '', $action), true));
			} else if ($action[0] === '@' && substr($action, -2) === '()') {
				return sprintf('[$control, %s]', var_export(str_replace('()', '', $action), true));
			}
			return var_export($action, true);
		}
	}
