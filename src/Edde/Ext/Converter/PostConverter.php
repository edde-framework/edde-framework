<?php
	declare(strict_types=1);

	namespace Edde\Ext\Converter;

	use Edde\Common\Converter\AbstractConverter;

	class PostConverter extends AbstractConverter {
		public function __construct() {
			$this->register('array', [
				'application/x-www-form-urlencoded',
			]);
		}

		/**
		 * @inheritdoc
		 */
		public function convert($content, string $mime, string $target = null) {
			$this->unsupported($content, $target, is_array($content));
			switch ($target) {
				case 'application/x-www-form-urlencoded':
					return http_build_query($content);
			}
			return $this->exception($mime, $target);
		}
	}
