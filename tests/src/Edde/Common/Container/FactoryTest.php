<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Common\Cache\CacheManager;
	use Edde\Ext\Cache\InMemoryCacheStorage;
	use Edde\Ext\Container\ClassFactory;
	use PHPUnit\Framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	class FactoryTest extends TestCase {
		public function testFactory() {
			$factoryManager = new FactoryManager(new CacheManager(new InMemoryCacheStorage()));
			$factoryManager->registerFactoryList([
//				'foo' => function ($a, $b) {
//				},
				new ClassFactory(),
			]);
			$factory = $factoryManager->getFactory(\Something::class);
			self::assertSame($factory->dependency(\Something::class), $dependency = $factory->dependency(\Something::class));
		}
	}
