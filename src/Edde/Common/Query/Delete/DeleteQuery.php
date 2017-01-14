<?php
	declare(strict_types = 1);

	namespace Edde\Common\Query\Delete;

	use Edde\Api\Container\IConfigurable;
	use Edde\Common\Container\ConfigurableTrait;
	use Edde\Common\Node\Node;
	use Edde\Common\Query\AbstractQuery;

	class DeleteQuery extends AbstractQuery implements IConfigurable {
		use ConfigurableTrait;
		/**
		 * @var string
		 */
		protected $source;

		/**
		 * @param string $source
		 */
		public function __construct($source) {
			$this->source = $source;
		}

		protected function handleInit() {
			$this->node = new Node('delete-query', $this->source);
		}
	}
