<?php
	declare(strict_types=1);

	namespace Edde\Ext\Application;

	use Edde\Common\Application\Response;

	class ExceptionResponse extends Response {
		public function __construct(\Exception $content, array $targetList = null) {
			parent::__construct($content, 'exception', $targetList);
		}
	}
