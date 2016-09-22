<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Macro;

	use Edde\Api\Crypt\ICryptEngine;
	use Edde\Api\Template\IHelper;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Template\HelperSet;

	class LoopMacro extends AbstractHtmlMacro implements IHelper {
		/**
		 * @var ICryptEngine
		 */
		protected $cryptEngine;

		public function __construct() {
			parent::__construct('loop', false);
		}

		public function lazyCryptEngine(ICryptEngine $cryptEngine) {
			$this->cryptEngine = $cryptEngine;
		}

		public function helper($value, ...$parameterList) {
			if ($value === '$:') {
				list($key, $value) = $this->compiler->getVariable(static::class)
					->top();
				return '$value_' . $value;
			} else if ($value === '$#') {
				list($key, $value) = $this->compiler->getVariable(static::class)
					->top();
				return '$key_' . $key;
			}
			return $value;
		}

		protected function onMacro() {
			$src = $this->attribute('src');
			/** @var $stack \SplStack */
			$stack = $this->compiler->getVariable(static::class, new \SplStack());
			$stack->push([
				$key = str_replace('-', '_', $this->cryptEngine->guid()),
				$value = str_replace('-', '_', $this->cryptEngine->guid()),
			]);
			$this->write(sprintf('foreach(%s as $key_%s => $value_%s) {', $this->loop($src), $key, $value), 5);
			$this->compile();
			$this->write('}', 5);
		}

		protected function loop(string $src) {
			if ($src[0] === '.') {
				return '$root->' . StringUtils::camelize(substr($src, 1), null, true);
			}
			return var_export($src, true);
		}

		protected function prepare() {
			parent::prepare();
			$this->helperSet = new HelperSet();
			$this->helperSet->registerHelper($this);
		}
	}
