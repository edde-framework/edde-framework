<?php
	declare(strict_types=1);

	namespace Edde\Ext\Application;

	use Edde\Common\Application\Response;

	class JsonResponse extends Response {
		public function __construct($content, array $targetList = null) {
			parent::__construct($content, 'json', $targetList);
		}
	}
