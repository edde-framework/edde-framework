<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IApplication;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Event\EventTrait;

	abstract class AbstractApplication extends AbstractDeffered implements IApplication {
		use EventTrait;
	}
