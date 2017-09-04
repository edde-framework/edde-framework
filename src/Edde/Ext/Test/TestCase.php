<?php
	declare(strict_types=1);

	namespace Edde\Ext\Test;

	use Edde\Api\Container\IAutowire;
	use Edde\Common\Container\AutowireTrait;
	use Edde\Ext\Container\ContainerFactory;

	class TestCase extends \PHPUnit\Framework\TestCase implements IAutowire {
		use AutowireTrait;

		protected function setUp() {
			ContainerFactory::autowire($this);
		}
	}
