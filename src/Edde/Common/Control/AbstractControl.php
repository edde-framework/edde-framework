<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control;

	use Edde\Api\Control\ControlException;
	use Edde\Api\Control\IControl;
	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Node\INode;
	use Edde\Common\Callback\Callback;
	use Edde\Common\Control\Event\DoneEvent;
	use Edde\Common\Control\Event\HandleEvent;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Event\EventTrait;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeIterator;

	abstract class AbstractControl extends AbstractDeffered implements IControl {
		use EventTrait;
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
			if ($this->node->isRoot()) {
				return $this;
			}
			return $this->node->getRoot()
				->getMeta('control');
		}

		public function getParent() {
			$this->use();
			$parent = $this->node->getParent();
			return $parent ? $parent->getMeta('control') : null;
		}

		public function isLeaf(): bool {
			$this->use();
			return $this->node->isLeaf();
		}

		public function disconnect(): IControl {
			$this->use();
			if ($this->node->isRoot() === false) {
				$this->node->getParent()
					->removeNode($this->node);
			}
			return $this;
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

		public function dirty(bool $dirty = true): IControl {
			$this->use();
			$this->node->setMeta('dirty', $dirty);
			foreach ($this->getControlList() as $control) {
				$control->dirty($dirty);
			}
			return $this;
		}

		public function getControlList() {
			$controlList = [];
			foreach ($this->node->getNodeList() as $node) {
				$controlList[] = $node->getMeta('control');
			}
			return $controlList;
		}

		/**
		 * @return IHtmlControl[]
		 */
		public function invalidate(): array {
			$invalidList = [];
			foreach ($this as $control) {
				if ($control->isDirty()) {
					$invalidList[] = $control;
				}
			}
			return $invalidList;
		}

		public function isDirty(): bool {
			$this->use();
			return $this->node->getMeta('dirty', false);
		}

		public function handle(string $method, array $parameterList) {
			$this->event(new HandleEvent($this, $method, $parameterList));
			$this->event(new DoneEvent($this, $result = $this->execute($method, $parameterList)));
			return $result;
		}

		protected function execute(string $method, array $parameterList) {
			$argumentList = array_filter($parameterList, function ($key) {
				return is_int($key);
			}, ARRAY_FILTER_USE_KEY);
			$callback = [
				$this,
				$method,
			];
			if (method_exists($this, $method)) {
				$callback = new Callback($callback);
				$argumentCount = count($argumentList);
				foreach ($callback->getParameterList() as $key => $parameter) {
					if (--$argumentCount >= 0) {
						continue;
					}
					if (isset($parameterList[$parameter->getName()]) === false) {
						if ($parameter->isOptional()) {
							continue;
						}
						throw new ControlException(sprintf('Missing action parameter [%s::%s(, ...$%s, ...)].', static::class, $method, $parameter->getName()));
					}
					$argumentList[] = $parameterList[$parameter->getName()];
				}
			}
			return $callback(...$argumentList);
		}

		public function getIterator() {
			$this->use();
			foreach (NodeIterator::recursive($this->node) as $node) {
				yield $node->getMeta('control');
			}
		}

		protected function prepare() {
			$this->node = new Node();
			$this->node->setMeta('control', $this);
			return $this;
		}
	}
