<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthenticator;
	use Edde\Common\AbstractObject;

	/**
	 * Abstract implementation for all authenticators.
	 */
	abstract class AbstractAuthenticator extends AbstractObject implements IAuthenticator {
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

		/**
		 * @inheritdoc
		 */
		public function getName(): string {
			return $this->name;
		}
	}
