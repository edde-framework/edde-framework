<?php
	declare(strict_types=1);

	namespace Edde\Common\Template\Macro;

	use Edde\Common\Template\AbstractMacro;

	class IncludeMacro extends AbstractMacro {
		/**
		 * @inheritdoc
		 */
		public function getNameList(): array {
			return ['include'];
		}
	}
