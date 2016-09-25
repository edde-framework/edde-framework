<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IInline;
	use Edde\Api\Template\MacroException;

	/**
	 * Abstract class for inline macro implementations.
	 */
	abstract class AbstractInline extends AbstractMacro implements IInline {
		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		protected function attribute(INode $macro, ICompiler $compiler, string $name = null, bool $helper = true) {
			if (($attribute = $macro->getAttribute($name = $name ?: $this->getName())) === null) {
				throw new MacroException(sprintf('Missing attribute [%s] in macro node [%s].', $name, $macro->getPath()));
			}
			$macro->removeAttribute($name);
			return $attribute;
		}
	}
