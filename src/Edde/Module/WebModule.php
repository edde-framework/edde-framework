<?php
	declare(strict_types = 1);

	namespace Edde\Module;

	use Edde\Api\Web\IJavaScriptCompiler;
	use Edde\Api\Web\IStyleSheetCompiler;
	use Edde\Common\Runtime\AbstractModule;
	use Edde\Common\Runtime\Event\SetupEvent;
	use Edde\Common\Web\JavaScriptCompiler;
	use Edde\Common\Web\StyleSheetCompiler;

	class WebModule extends AbstractModule {
		public function setupWebModule(SetupEvent $setupEvent) {
			$runtime = $setupEvent->getRuntime();
			$runtime->registerFactoryList([
				IStyleSheetCompiler::class => StyleSheetCompiler::class,
				IJavaScriptCompiler::class => JavaScriptCompiler::class,
			]);
		}
	}
