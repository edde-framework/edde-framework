<?php
	declare(strict_types=1);

	namespace Edde\Ext\Application;

	use Edde\Common\Application\Response;

	class ExceptionResponse extends Response {
		/**
		 * @param \Exception $exception
		 * @param array|null $targetList
		 */
		public function __construct(\Exception $exception, array $targetList = null) {
			parent::__construct($exception, 'exception', $targetList);
		}
	}
