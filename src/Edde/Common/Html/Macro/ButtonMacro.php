<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Common\Html\Tag\ButtonControl;

	/**
	 * Special case of Control macro for button control support.
	 */
	class ButtonMacro extends HtmlMacro {
		/**
		 * BASIC is to computer programming as "qwerty" is to typing.
		 */
		public function __construct() {
			parent::__construct('button', ButtonControl::class);
		}

		/**
		 * @inheritdoc
		 */
		protected function onControl(INode $macro, ICompiler $compiler) {
			if (($action = $this->extract($macro, 'action')) !== null) {
				$this->write($compiler, sprintf('$control->setAction(%s);', $this->action($action)), 5);
			}
		}

		/**
		 * @param string $action
		 *
		 * @return mixed|string
		 */
		protected function action(string $action) {
			if (substr($action, -2) === '()') {
				$type = $action[0];
				$action = var_export(str_replace('()', '', substr($action, 1)), true);
				return sprintf('[%s, %s]', self::$reference[$type], $action);
			}
			return var_export($action, true);
		}
	}
