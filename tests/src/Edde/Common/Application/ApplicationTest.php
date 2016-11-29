<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\ApplicationException;
	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponseManager;
	use Edde\Api\Log\ILogService;
	use Edde\Common\Application\Event\FinishEvent;
	use Edde\Common\Application\Event\StartEvent;
	use Edde\Common\Container\Factory\ReflectionFactory;
	use Edde\Common\Log\LogService;
	use Edde\Ext\Container\ContainerFactory;
	use PHPUnit\Framework\TestCase;

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
		 * @var IRequest
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
			$this->request->registerActionHandler(\SomeControl::class, 'executeThisMethod', ['poo' => 'return this as result']);
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

		public function testForbiddenControl() {
			$this->expectException(ApplicationException::class);
			$this->expectExceptionMessage('Route class [ForbiddenControl] is not instance of [Edde\Api\Control\IControl].');
			$this->request->registerActionHandler(\ForbiddenControl::class, 'foo', []);
			$this->application->run();
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IApplication::class => Application::class,
				IResponseManager::class => ResponseManager::class,
				IRequest::class => function () {
					return new Request('foo');
				},
				ILogService::class => LogService::class,
				\SomeControl::class => new ReflectionFactory(\SomeControl::class, \SomeControl::class, true),
				IErrorControl::class => \SomeErrorControl::class,
			]);
			$this->request = $container->create(IRequest::class);
			$this->control = $container->create(\SomeControl::class);
			$this->errorControl = $container->create(IErrorControl::class);
			$this->application = $container->create(IApplication::class);
		}
	}
