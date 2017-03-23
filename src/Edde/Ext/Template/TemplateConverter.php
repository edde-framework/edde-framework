<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Template\ITemplate;
	use Edde\Common\Converter\AbstractConverter;

	class TemplateConverter extends AbstractConverter {
		public function __construct() {
			$this->register(ITemplate::class, [
				'text/html',
				'string',
			]);
		}

		/**
		 * @inheritdoc
		 *
		 * @param ITemplate $content
		 */
		public function convert($content, string $mime, string $target) {
			$this->unsupported($content, $target, $content instanceof ITemplate);
			switch ($target) {
				case 'text/html':
				case 'string':
					$content->execute();
			}
		}
	}
