<?php
	namespace Edde\Common\Control;

	use Edde\Api\Control\IControl;
	use Edde\Api\Node\INode;
	use Edde\Common\Node\Node;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractControl extends AbstractUsable implements IControl {
		/**
		 * @var INode
		 */
		protected $node;

		public function getNode() {
			$this->usse();
			return $this->node;
		}

		public function addControl(IControl $control) {
			$this->usse();
			$this->node->addNode($control->getNode());
			return $this;
		}

		public function getControlList() {
			foreach ($this->node->getNodeList() as $node) {
				yield $node->getMeta('control');
			}
		}

		protected function prepare() {
			$this->node = new Node();
			$this->node->setMeta('control', $this);
			$this->onPrepare();
		}

		abstract protected function onPrepare();
	}
