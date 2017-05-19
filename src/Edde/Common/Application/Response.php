<?php
	declare(strict_types=1);

	namespace Edde\Common\Application;

	use Edde\Common\Converter\Content;

	class Response extends Content {
		/**
		 * @var string[]
		 */
		protected $targetList;

		public function __construct($content, string $mime, array $targetList = null) {
			parent::__construct($content, $mime);
			$this->targetList = $targetList;
		}

		/**
		 * @inheritdoc
		 */
		public function getTargetList() {
			return $this->targetList;
		}
	}
