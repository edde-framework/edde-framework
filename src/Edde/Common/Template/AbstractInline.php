<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IInline;
	use Edde\Api\Template\MacroException;

	abstract class AbstractInline extends AbstractMacro implements IInline {
		public function attribute(INode $macro, string $name) {
			if (($attribute = $macro->getAttribute($name)) === null) {
				throw new MacroException(sprintf('Missing attribute [%s] in macro node [%s].', $name, $macro->getPath()));
			}
			$macro->removeAttribute($name);
			return $attribute;
		}
	}
