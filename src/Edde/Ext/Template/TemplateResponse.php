<?php
	declare(strict_types=1);

	namespace Edde\Ext\Template;

	use Edde\Api\Template\ITemplate;
	use Edde\Common\Application\Response;

	class TemplateResponse extends Response {
		public function __construct(ITemplate $template, array $targetList = null) {
			parent::__construct($template, ITemplate::class, $targetList);
		}
	}