<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control;

	use Edde\Api\Callback\ICallback;
	use Edde\Api\Control\ControlException;
	use Edde\Api\Control\IControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\Callback\Callback;
	use Edde\Common\Control\Event\DoneEvent;
	use Edde\Common\Control\Event\HandleEvent;
	use Edde\Common\Deffered\AbstractDeffered;
	use Edde\Common\Event\EventTrait;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Root implementation of all controls.
	 */
	abstract class AbstractControl extends AbstractDeffered implements IControl {
		use EventTrait;
		/**
		 * @var INode
		 */
		protected $node;

		/**
		 * @inheritdoc
		 */
		public function getNode() {
			$this->use();
			return $this->node;
		}

		/**
		 * @inheritdoc
		 */
		public function getRoot() {
			$this->use();
			if ($this->node->isRoot()) {
				return $this;
			}
			/** @var $rootNode INode */
			$rootNode = $this->node->getRoot();
			return $rootNode->getMeta('control');
		}

		/**
		 * @inheritdoc
		 */
		public function getParent() {
			$this->use();
			$parent = $this->node->getParent();
			return $parent ? $parent->getMeta('control') : null;
		}

		/**
		 * @inheritdoc
		 */
		public function isLeaf(): bool {
			$this->use();
			return $this->node->isLeaf();
		}

		/**
		 * @inheritdoc
		 */
		public function disconnect(): IControl {
			$this->use();
			if ($this->node->isRoot() === false) {
				$this->node->getParent()
					->removeNode($this->node);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function addControlList(array $controlList) {
			foreach ($controlList as $control) {
				$this->addControl($control);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function addControl(IControl $control) {
			$this->use();
			$this->node->addNode($control->getNode(), true);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function dirty(bool $dirty = true): IControl {
			$this->use();
			$this->node->setMeta('dirty', $dirty);
			foreach ($this->getControlList() as $control) {
				$control->dirty($dirty);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getControlList() {
			$controlList = [];
			foreach ($this->node->getNodeList() as $node) {
				$controlList[] = $node->getMeta('control');
			}
			return $controlList;
		}

		/**
		 * @inheritdoc
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

		/**
		 * @inheritdoc
		 */
		public function isDirty(): bool {
			$this->use();
			return $this->node->getMeta('dirty', false);
		}

		/**
		 * @inheritdoc
		 * @throws ControlException
		 */
		public function handle(string $method, array $parameterList) {
			$this->listen($this);
			$this->event(new HandleEvent($this, $method, $parameterList));
			$this->event(new DoneEvent($this, $result = $this->execute($method, $parameterList)));
			return $result;
		}

		/**
		 * @param string $method
		 * @param array $parameterList
		 *
		 * @return mixed
		 * @throws ControlException
		 */
		protected function execute(string $method, array $parameterList) {
			$argumentList = array_filter($parameterList, function ($key) {
				return is_int($key);
			}, ARRAY_FILTER_USE_KEY);
			$callback = [
				$this,
				$method,
			];
			if (method_exists($this, $method)) {
				/** @var $callback ICallback */
				$callback = new Callback($callback);
				$argumentCount = count($argumentList);
				foreach ($callback->getParameterList() as $key => $parameter) {
					if (--$argumentCount >= 0) {
						continue;
					}
					if (isset($parameterList[$parameterName = StringUtils::recamel($parameter->getName())]) === false) {
						if ($parameter->isOptional()) {
							continue;
						}
						throw new ControlException(sprintf('Missing action parameter [%s::%s(, ...$%s, ...)].', static::class, $method, $parameter->getName()));
					}
					$argumentList[] = $parameterList[$parameterName];
				}
			}
			return $callback(...$argumentList);
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function getIterator() {
			$this->use();
			foreach (NodeIterator::recursive($this->node) as $node) {
				yield $node->getMeta('control');
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function prepare() {
			$this->node = new Node();
			$this->node->setMeta('control', $this);
			return $this;
		}
	}
