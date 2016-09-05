<?php
	declare(strict_types = 1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IApplication;
	use Edde\Api\Application\IErrorControl;
	use Edde\Api\Control\IControl;
	use Edde\Api\Router\IRoute;
	use Edde\Common\Container\Factory\ClassFactory;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class ApplicationTest extends TestCase {
		/**
		 * @var IApplication
		 */
		protected $application;
		/**
		 * @var \SomeRoute
		 */
		protected $route;
		/**
		 * @var \SomeControl
		 */
		protected $control;
		/**
		 * @var \SomeErrorControl
		 */
		protected $errorControl;

		public function testWorkflow() {
			$this->route->class = \SomeControl::class;
			$this->route->method = 'executeThisMethod';
			$this->route->parameters = ['poo' => 'return this as result'];
			self::assertEquals('return this as result', $this->application->run());
		}

		public function testErrorControl() {
			$this->control->throw();
			$this->route->class = \SomeControl::class;
			$this->route->method = 'executeThisMethod';
			$this->route->parameters = ['poo' => 'return this as result'];
			self::assertInstanceOf(\Exception::class, $this->application->run());
			self::assertNotEmpty($exception = $this->errorControl->getException());
			self::assertEquals('some error', $exception->getMessage());
		}

		public function testForbiddenControl() {
			$this->route->class = \ForbiddenControl::class;
			$this->route->method = 'foo';
			$this->route->parameters = [];
			$this->application->run();
			self::assertInstanceOf(\Exception::class, $exception = $this->errorControl->getException());
			self::assertEquals(sprintf('Route class [ForbiddenControl] is not instance of [%s].', IControl::class), $exception->getMessage());
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IApplication::class => Application::class,
				IRoute::class => function () {
					return new \SomeRoute();
				},
				\SomeControl::class => new ClassFactory(\SomeControl::class, \SomeControl::class, true),
				IErrorControl::class => \SomeErrorControl::class,
			]);
			$this->route = $container->create(IRoute::class);
			$this->control = $container->create(\SomeControl::class);
			$this->errorControl = $container->create(IErrorControl::class);
			$this->application = $container->create(IApplication::class);
		}
	}
