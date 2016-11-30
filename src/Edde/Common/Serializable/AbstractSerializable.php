<?php
	declare(strict_types = 1);

	namespace Edde\Common\Serializable;

	use Edde\Api\Serializable\ISerializable;
	use Edde\Common\AbstractObject;

	abstract class AbstractSerializable extends AbstractObject implements ISerializable {
		public function warmup(): ISerializable {
			return $this;
		}

		public function serialize(): string {
			return '';
		}
	}
