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
			if ($this->compiler) {
				/** @var $stack \SplStack */
				$stack = $this->compiler->getVariable(static::class);
			}
			if ($value === null) {
				return null;
			} else if ($value === '$:') {
				list($key, $value) = $stack->top();
				return '$value_' . $value;
			} else if ($value === '$#') {
				list($key, $value) = $stack->top();
				return '$key_' . $key;
			} else if ($match = StringUtils::match($value, '~\$(?<type>:|#)(?<jump>\$|\.|\d+)?(\->(?<call>[a-z0-9-]+\(\)))?~', true, true)) {
				$jump = $match['jump'] ?? 0;
				$type = $match['type'];
				if ($jump === '$') {
					$jump = 1;
				} else if ($jump === '.') {
					$jump = $stack->count();
				}
				foreach ($stack as $loop) {
					if ($jump-- <= 0) {
						break;
					}
				}
				list($key, $value) = $loop;
				if ($type === '#') {
					return '$key_' . $key;
				}
				return '$value_' . $value . (isset($match['call']) ? '->' . StringUtils::camelize($match['call'], null, true) : '');
			}
			return null;
		}

		protected function jump($jump, $variable, bool $value) {
			/** @var $stack \SplStack */
			$stack = $this->compiler->getVariable(static::class);
			if ($match = StringUtils::match($jump, '~\$:(?<jump>\$?\-\d+?)?(?<method>.*)\(\)~')) {
				list($key, $value) = $stack->top();
				return '$value_' . $value . '->' . StringUtils::camelize(str_replace('->', '', $jump), null, true);
			} else if ($jump === '$') {
				$jump = 1;
			} else if ($jump === '.') {
				$jump = $stack->count();
			}
			foreach ($stack as $loop) {
				if ($jump-- <= 0) {
					break;
				}
			}
			return '$' . $variable . $loop[(int)$value];
		}

		protected function onMacro() {
			$src = $this->attribute('src');
			/** @var $stack \SplStack */
			$stack = $this->compiler->getVariable(static::class, new \SplStack());
			$loop = [
				$key = str_replace('-', '_', $this->cryptEngine->guid()),
				$value = str_replace('-', '_', $this->cryptEngine->guid()),
			];
			$this->write('$control = $stack->top();', 5);
			$this->write(sprintf('foreach(%s as $key_%s => $value_%s) {', $this->loop($src), $key, $value), 5);
			$stack->push($loop);
			$this->compile();
			$stack->pop();
			$this->write('}', 5);
		}

		protected function loop(string $src) {
			if ($src[0] === '.') {
				return '$root->' . StringUtils::camelize(substr($src, 1), null, true);
			} else if ($src[0] === '@') {
				return '$control->' . StringUtils::camelize(substr($src, 1), null, true);
			} else if ($src === '$:') {
				list($key, $value) = $this->compiler->getVariable(static::class)
					->top();
				return '$value_' . $value;
			}
			return var_export($src, true);
		}

		protected function prepare() {
			parent::prepare();
			$this->helperSet = new HelperSet();
			$this->helperSet->registerHelper($this);
		}
	}
