<?php
	declare(strict_types = 1);

	namespace Edde\Test;

	use Edde\Api\Serialize\IHashable;
	use Edde\Api\Serialize\ISerializable;
	use Edde\Common\Object;
	use Edde\Common\Serialize\SerializableTrait;

	class FooObject extends Object implements ISerializable, IHashable {
		use SerializableTrait;

		public $foo = 'a';
	}
