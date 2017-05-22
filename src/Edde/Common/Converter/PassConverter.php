<?php
	declare(strict_types=1);

	namespace Edde\Common\Converter;

	class PassConverter extends AbstractConverter {
		/**
		 * @inheritdoc
		 */
		public function convert($content, string $mime, string $target = null) {
			return $content;
		}
	}
