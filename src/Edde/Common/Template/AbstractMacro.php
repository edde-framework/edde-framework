<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\MacroException;
	use Edde\Common\AbstractObject;

	abstract class AbstractMacro extends AbstractObject implements IMacro {
		/**
		 * @var string
		 */
		protected $name;

		public function __construct(string $name) {
			$this->name = $name;
		}

		public function getName(): string {
			return $this->name;
		}

		public function attribute(INode $macro, string $name) {
			if (($attribute = $macro->getAttribute($name)) === null) {
				throw new MacroException(sprintf('Missing attribute [%s] in macro node [%s].', $name, $macro->getPath()));
			}
			return $attribute;
		}

	}
