<?php
	declare(strict_types=1);

	namespace Edde\Ext\Application;

	use Edde\Common\Application\Response;

	/**
	 * Basically text/plain response.
	 */
	class StringResponse extends Response {
		public function __construct(string $content, array $targetList = null) {
			parent::__construct($content, 'text/plain', $targetList);
		}
	}
