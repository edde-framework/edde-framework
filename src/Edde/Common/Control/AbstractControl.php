<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control;

	use Edde\Api\Control\IControl;
	use Edde\Api\Node\INode;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractControl extends AbstractUsable implements IControl {
		/**
		 * @var INode
		 */
		protected $node;

		public function getNode() {
			$this->use();
			return $this->node;
		}

		public function getRoot() {
			$this->use();
			return $this->node->getRoot()
				->getMeta('control');
		}

		public function getParent() {
			$this->use();
			$parent = $this->node->getParent();
			return $parent ? $parent->getMeta('control') : null;
		}

		/**
		 * @param IControl[] $controlList
		 *
		 * @return $this
		 */
		public function addControlList(array $controlList) {
			foreach ($controlList as $control) {
				$this->addControl($control);
			}
			return $this;
		}

		public function addControl(IControl $control) {
			$this->use();
			$this->node->addNode($control->getNode(), true);
			return $this;
		}

		public function getControlList() {
			$controlList = [];
			foreach ($this->node->getNodeList() as $node) {
				$controlList[] = $node->getMeta('control');
			}
			return $controlList;
		}

		public function dirty(bool $dirty = true): IControl {
			$this->use();
			$this->node->setMeta('dirty', $dirty);
			foreach ($this as $control) {
				$control->dirty($dirty);
			}
			return $this;
		}

		public function isDirty(): bool {
			$this->use();
			return $this->node->getMeta('dirty', false);
		}

		public function getIterator() {
			$this->use();
			foreach (NodeIterator::create($this->node) as $node) {
				yield $node->getMeta('control');
			}
		}

		protected function prepare() {
			$this->node = new Node();
			$this->node->setMeta('control', $this);
		}
	}
