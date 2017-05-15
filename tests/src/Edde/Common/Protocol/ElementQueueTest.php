<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\File\IRootDirectory;
	use Edde\Api\Protocol\LazyElementQueueTrait;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Common\File\RootDirectory;
	use Edde\Common\Protocol\Event\Event;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;

	require_once __DIR__ . '/../assets/assets.php';

	class ElementQueueTest extends TestCase {
		use LazyElementQueueTrait;

		public function testElementQueue() {
			self::assertEmpty(iterator_to_array($this->elementQueue->getQueueList()));
			$this->elementQueue->queue(new Event('some-cool-event'));
			self::assertCount(1, iterator_to_array($this->elementQueue->getQueueList()));
			$this->elementQueue->clear();
			self::assertEmpty(iterator_to_array($this->elementQueue->getQueueList()));
			$this->elementQueue->queue(new Event('some-cool-event'));
			$this->elementQueue->save();
			$this->elementQueue->clear();
			self::assertEmpty(iterator_to_array($this->elementQueue->getQueueList()));
			$this->elementQueue->load();
			self::assertCount(1, iterator_to_array($this->elementQueue->getQueueList()));
		}

		protected function setUp() {
			ContainerFactory::autowire($this, [
				IRootDirectory::class => ContainerFactory::instance(RootDirectory::class, [__DIR__ . '/temp']),
				new ClassFactory(),
			]);
		}
	}
