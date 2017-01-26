<?php
	declare(strict_types=1);

	namespace Edde\Ext\Application;

	use Edde\Common\Application\Response;

	class ArrayResponse extends Response {
		public function __construct(array $content, array $targetList = null) {
			parent::__construct($content, 'array', $targetList);
		}
	}
