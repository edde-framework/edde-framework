<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Router;

	use Edde\Api\Runtime\IRuntime;
	use Edde\Common\Cli\CliUtils;
	use Edde\Common\Router\AbstractRouter;

	class CliRouter extends AbstractRouter {
		/**
		 * @var IRuntime
		 */
		protected $runtime;

		/**
		 * @param IRuntime $runtime
		 */
		public function __construct(IRuntime $runtime) {
			$this->runtime = $runtime;
		}

		public function route() {
			$this->use();
			if ($this->runtime->isConsoleMode() === false) {
				return null;
			}
			$argList = CliUtils::getArgumentList(array_slice($_SERVER['argv'], 1));
			$actionList = explode(':', isset($argList[0]) && $_SERVER['argv'][1] === $argList[0] ? $argList[0] : 'index:index:index');
			$action = array_pop($actionList);
			$presenter = 'index';
			if (isset($actionList[0])) {
				$presenter = ($presenter = array_pop($actionList)) ?: 'index';
			}
			if (isset($actionList[0])) {
				array_walk($actionList, function (&$value) {
					$value = $value ?: 'index';
				});
			}
			throw new \Exception('not implemented yet: cli router');
		}

		protected function prepare() {
		}
	}
