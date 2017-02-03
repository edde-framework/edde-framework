<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Object;

	abstract class AbstractMacro extends Object implements IMacro {
		public function register(ITemplate $template): IMacro {
			foreach ($this->getNameList() as $name) {
				$template->registerMacro($name, $this);
			}
			return $this;
		}
	}
