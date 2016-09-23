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
			if (($action = $this->extract($macro, 'action', null, false)) !== null) {
				$this->write(sprintf('$control->setAction(%s);', $this->action($action)), 5);
			}
		}

		protected function action(string $action) {
			if (substr($action, -2) === '()') {
				$type = $action[0];
				$action = var_export(str_replace('()', '', substr($action, 1)), true);
				if ($type === '.') {
					return sprintf('[$root, %s]', $action);
				} else if ($type === '@') {
					return sprintf('[$control, %s]', $action);
				}
			}
			return var_export($action, true);
		}
	}
