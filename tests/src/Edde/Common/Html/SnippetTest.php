<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Template\TemplateManager;
	use Edde\Ext\Container\ContainerFactory;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class SnippetTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var IHtmlControl
		 */
		protected $htmlControl;

		public function testSnippet() {
			self::assertTrue(true);
//			$this->htmlControl->snippet($this->container->create(DivControl::class), 'snippet');
		}

		protected function setUp() {
			$this->container = ContainerFactory::create([
				ITemplateManager::class => TemplateManager::class,
			]);
			$this->htmlControl = $this->container->create(\MyLittleCuteView::class);
		}
	}
