<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Template\IInline;
	use Edde\Api\Template\MacroException;

	abstract class AbstractInline extends AbstractMacro implements IInline {
		protected function attribute(string $name = null) {
			if (($attribute = $this->macro->getAttribute($name = $name ?: $this->getName())) === null) {
				throw new MacroException(sprintf('Missing attribute [%s] in macro node [%s].', $name, $this->macro->getPath()));
			}
			$this->macro->removeAttribute($name);
			return $attribute;
		}
	}
