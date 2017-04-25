<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\IEventBus;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Object;

	class EventBus extends Object implements IEventBus {
		use ConfigurableTrait;
	}
