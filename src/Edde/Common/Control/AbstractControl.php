<?php
	declare(strict_types = 1);

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
			$this->usse();
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

		protected function prepare() {
			$this->node = new Node();
			$this->node->setMeta('control', $this);
		}
	}
