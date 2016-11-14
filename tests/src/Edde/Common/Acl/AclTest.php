<?php
	declare(strict_types = 1);

	namespace Edde\Common\Acl;

	use Edde\Api\Acl\IAclManager;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	class AclTest extends TestCase {
		/**
		 * @var IAclManager
		 */
		protected $aclManager;

		public function testRootAcl() {
			$acl = $this->aclManager->acl(['root']);
			self::assertTrue($acl->can('something'), 'Root has missing right!');
			self::assertFalse($acl->can('be-stupid'), 'Root can be stupid, oops!');
		}

		protected function setUp() {
			$container = ContainerFactory::create([
				IAclManager::class => AclManager::class,
			]);
			$this->aclManager = $container->create(IAclManager::class);
			$this->aclManager->grant('root');
			$this->aclManager->deny('be-stupid');
			$this->aclManager->deny('guest');
			$this->aclManager->grant('guest', 'file.read');
		}
	}
