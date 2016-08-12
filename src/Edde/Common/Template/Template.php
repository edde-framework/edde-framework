<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\ITemplate;
	use Edde\Common\Usable\AbstractUsable;

	class Template extends AbstractUsable implements ITemplate {
		/**
		 * @var string
		 */
		protected $name;

		/**
		 * @param string $name
		 */
		public function __construct(string $name) {
			$this->name = $name;
		}

		protected function prepare() {
		}
	}
