<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\ITemplateManager;
	use Edde\Common\Cache\CacheTrait;
	use Edde\Common\Usable\AbstractUsable;

	class TemplateManager extends AbstractUsable implements ITemplateManager {
		use CacheTrait;

		protected function prepare() {
			$this->cache();
		}
	}
