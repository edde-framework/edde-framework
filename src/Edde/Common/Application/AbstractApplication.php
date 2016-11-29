<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IApplication;
	use Edde\Common\AbstractObject;
	use Edde\Common\Event\EventTrait;

	/**
	 * Common implementation for all applications.
	 */
	abstract class AbstractApplication extends AbstractObject implements IApplication {
		use EventTrait;
	}
