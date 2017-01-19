<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Api\Application\IResponse;
	use Edde\Common\Converter\Content;

	class Response extends Content implements IResponse {
		/**
		 * @var string[]
		 */
		protected $targetList;

		public function __construct($content, string $mime, array $targetList) {
			parent::__construct($content, $mime);
			$this->targetList = $targetList;
		}

		/**
		 * @inheritdoc
		 */
		public function getTargetList(): array {
			return $this->targetList;
		}
	}
