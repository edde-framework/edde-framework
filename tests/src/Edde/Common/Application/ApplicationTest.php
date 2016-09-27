<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Control\IControl;
	use Edde\Common\Application\Event\FinishEvent;
	use Edde\Common\Application\Event\StartEvent;
	use Edde\Common\Container\Factory\ReflectionFactory;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once __DIR__ . '/assets/assets.php';

	/**
	 * Tests related to an application. Surprise!
	 */
	class ApplicationTest extends TestCase {
		/**
		 * @var Application
		 */
		protected $application;
		/**
		 * @var \SomeRequest
		 */
		protected $request;
		/**
		 * @var \SomeControl
		 */
		protected $control;
		/**
		 * @var \SomeErrorControl
		 */
		protected $errorControl;

		public function testWorkflow() {
			$this->request->class = \SomeControl::class;
			$this->request->method = 'executeThisMethod';
			$this->request->parameters = ['poo' => 'return this as result'];
			$eventList = [];
			$this->application->listen(function (StartEvent $startEvent) use (&$eventList) {
				$eventList[] = get_class($startEvent);
			});
			$this->application->listen(function (FinishEvent $finishEvent) use (&$eventList) {
				$eventList[] = get_class($finishEvent);
			});
			self::assertEquals('return this as result', $this->application->run());
			self::assertEquals([
				StartEvent::class,
				FinishEvent::class,
			], $eventList);
		}

		public function testErrorControl() {
			$this->control->throw();
			$this->request->class = \SomeControl::class;
			$this->request->method = 'executeThisMethod';
			$this->request->parameters = ['poo' => 'return this as result'];
			self::assertInstanceOf(\Exception::class, $this->application->run());
			self::assertNotEmpty($exception = $this->errorControl->getException());
			self::assertEquals('some error', $exception->getMessage());
		}

		public function testForbiddenControl() {
			$this->request->class = \ForbiddenControl::class;
			$this->request->method = 'foo';
			$this->request->parameters = [];
			$this->application->run();
			self::assertInstanceOf(\Exception::class, $exception = $this->errorControl->getException());
			self::assertEquals(sprintf('Route class [ForbiddenControl] is not instance of [%s].', IControl::class), $exception->getMessage());
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IApplication::class => Application::class,
				IResponseManager::class => ResponseManager::class,
				IRequest::class => function () {
					return new \SomeRequest();
				},
				\SomeControl::class => new ReflectionFactory(\SomeControl::class, \SomeControl::class, true),
				IErrorControl::class => \SomeErrorControl::class,
			]);
			$this->request = $container->create(IRequest::class);
			$this->control = $container->create(\SomeControl::class);
			$this->errorControl = $container->create(IErrorControl::class);
			$this->application = $container->create(IApplication::class);
		}
	}
