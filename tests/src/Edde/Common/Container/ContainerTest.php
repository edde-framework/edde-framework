<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Ext\Container\CallbackFactory;
	use PHPUnit\Framework\TestCase;

	class ContainerTest extends TestCase {
		public function testContainer() {
			$container = new Container();
			$container->registerFactory(new CallbackFactory(function () {
				return 'bar';
			}, 'foo'));
			self::assertEquals('bar', $container->create('foo'));
			self::assertEquals('bar', $container->call(function ($foo) {
				return $foo;
			}));
		}
	}
