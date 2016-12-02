<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container\Factory;

	use Edde\Api\Container\IFactory;
	use Edde\Common\AbstractObject;

	/**
	 * Basic implementation for all dependency factories.
	 */
	abstract class AbstractFactory extends AbstractObject implements IFactory {
	}
