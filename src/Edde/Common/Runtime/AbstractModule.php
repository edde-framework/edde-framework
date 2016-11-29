<?php
	declare(strict_types = 1);

	namespace Edde\Common\Runtime;

	use Edde\Api\Runtime\IModule;
	use Edde\Common\Event\Handler\SelfHandler;

	abstract class AbstractModule extends SelfHandler implements IModule {
	}
