<?php
	declare(strict_types = 1);

	namespace Edde\Common\Identity;

	use Edde\Api\Identity\IAuthenticator;
	use Edde\Common\Deffered\AbstractDeffered;

	abstract class AbstractAuthenticator extends AbstractDeffered implements IAuthenticator {
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
