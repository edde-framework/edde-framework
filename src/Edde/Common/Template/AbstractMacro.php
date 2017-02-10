<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Object;

	abstract class AbstractMacro extends Object implements IMacro {
		public function inline(ITemplate $template, INode $node, string $name, string $value = null) {
		}

		public function open(ITemplate $template, INode $node) {
		}

		public function macro(ITemplate $template, INode $node) {
		}

		public function close(ITemplate $template, INode $node) {
		}

		public function register(ITemplate $template): IMacro {
			foreach ($this->getNameList() as $name) {
				$template->registerMacro($name, $this);
			}
			return $this;
		}
	}
