<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuth;
	use Edde\Common\Usable\AbstractUsable;

	/**
	 * @internal common stuff for Auth.
	 */
	abstract class AbstractAuth extends AbstractUsable implements IAuth {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param string $name
		 */
		public function __construct(string $name = null) {
			$this->name = $name ?: static::class;
		}

		public function getName(): string {
			return $this->name;
		}
	}
