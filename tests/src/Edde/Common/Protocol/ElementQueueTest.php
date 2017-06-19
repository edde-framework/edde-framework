<?php
	declare(strict_types=1);

	namespace Edde\Common\Protocol;

	use Edde\Api\Protocol\LazyElementQueueTrait;
	use Edde\Api\Store\LazyStoreTrait;
	use Edde\Common\Protocol\Event\Event;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;

	require_once __DIR__ . '/../assets/assets.php';

	class ElementQueueTest extends TestCase {
		use LazyElementQueueTrait;
		use LazyStoreTrait;

		public function testElementQueue() {
			$this->store->drop();
			self::assertEmpty(iterator_to_array($this->elementQueue->getElementList()));
			$this->elementQueue->queue(new Event('some-cool-event'));
			self::assertCount(1, iterator_to_array($this->elementQueue->getElementList()));
			$this->elementQueue->clear();
			self::assertEmpty(iterator_to_array($this->elementQueue->getElementList()));
			$this->elementQueue->queue(new Event('some-cool-event'));
			$this->elementQueue->save();
			$this->elementQueue->clear();
			self::assertEmpty(iterator_to_array($this->elementQueue->getElementList()));
			$this->elementQueue->load();
			self::assertCount(1, iterator_to_array($this->elementQueue->getElementList()));
		}

		protected function setUp() {
			ContainerFactory::autowire($this);
		}
	}
