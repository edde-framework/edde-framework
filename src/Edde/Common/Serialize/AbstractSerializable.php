<?php
	declare(strict_types = 1);

	namespace Edde\Common\Serialize;

	use Edde\Api\Serialize\ISerializable;
	use Edde\Common\Object;

	class AbstractSerializable extends Object implements ISerializable {
		use SerializableTrait;
	}
