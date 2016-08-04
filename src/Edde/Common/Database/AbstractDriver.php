<?php
	declare(strict_types = 1);

	namespace Edde\Common\Database;

	use Edde\Api\Database\DriverException;
	use Edde\Api\Database\IDriver;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractDriver extends AbstractUsable implements IDriver {
		/**
		 * @var string[]
		 */
		protected $typeList;

		protected function setTypeList(array $typeList) {
			static $list = [
				/** special cases */
				null => null,
				/** standard set of "scalar" types */
				'int' => null,
				'float' => null,
				'long' => null,
				'string' => null,
				'text' => null,
				'datetime' => null,
			];;
			$unknown = array_diff($typeKeys = array_keys($typeList), $listKeys = array_keys($list));
			if (empty($unknown) === false) {
				throw new DriverException(sprintf('Driver [%s] has set unknown type(s) [%s].', static::class, implode(', ', $unknown)));
			}
			$missing = array_diff($listKeys, $typeKeys);
			if (empty($missing) === false) {
				throw new DriverException(sprintf('Driver [%s] has not set mandatory type(s) [%s].', static::class, implode(', ', $missing)));
			}
			$this->typeList = $typeList;
			return $this;
		}
	}
