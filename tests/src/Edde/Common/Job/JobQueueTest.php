<?php
	declare(strict_types=1);

	namespace Edde\Common\Job;

	use Edde\Api\Job\LazyJobQueueTrait;
	use Edde\Ext\Container\ContainerFactory;
	use Edde\Ext\Test\TestCase;

	class JobQueueTest extends TestCase {
		use LazyJobQueueTrait;

		public function testJobQueue() {
			self::assertFalse($this->jobQueue->hasJob());
		}

		protected function setUp() {
			ContainerFactory::autowire($this);
		}
	}
