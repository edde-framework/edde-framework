<?php
	declare(strict_types=1);

	namespace Edde\Common\Lock;

	use Edde\Api\Lock\ILock;
	use Edde\Common\Object;

	abstract class AbstractLock extends Object implements ILock {
		/**
		 * @var string
		 */
		protected $id;
		/**
		 * @var string
		 */
		protected $source;

		public function __construct(string $id, string $source) {
			$this->id = $id;
			$this->source = $source;
		}

		/**
		 * @inheritdoc
		 */
		public function getId(): string {
			return $this->id;
		}

		/**
		 * @inheritdoc
		 */
		public function getSource(): string {
			return $this->source;
		}
	}
