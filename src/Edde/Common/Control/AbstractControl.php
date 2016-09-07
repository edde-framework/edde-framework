<?php
	declare(strict_types = 1);

	namespace Edde\Common\Control;

	use Edde\Api\Control\ControlException;
	use Edde\Api\Control\IControl;
	use Edde\Api\Node\INode;
	use Edde\Common\Callback\Callback;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Usable\AbstractUsable;

	abstract class AbstractControl extends AbstractUsable implements IControl {
		/**
		 * @var INode
		 */
		protected $node;
		/**
		 * @var callable[]
		 */
		protected $snippetList = [];
		/**
		 * already called snippets
		 *
		 * @var array
		 */
		protected $snippets = [];

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

		public function addSnippet(string $name, callable $snippet, callable $callback = null): IControl {
			$this->snippetList[$name] = [
				/** callable */
				$snippet,
				/** parent */
				null,
				/** returned snippet control */
				null,
				/** invalidator callback */
				$callback,
			];
			return $this;
		}

		public function snippet(string $name, IControl $parent = null): IControl {
			if (isset($this->snippetList[$name]) === false) {
				throw new ControlException(sprintf('Requested unknown snippet [%s] on control [%s].', $name, static::class));
			}
			$parent = $parent ?: $this;
			$snippet = &$this->snippetList[$name];
			if ($snippet[2] !== null) {
				throw new ControlException(sprintf('Snippet [%s] was already called on control [%s].', $name, static::class));
			}
			$snippet[1] = $parent;
			$snippet[2] = $snippet[0]($parent);
			return $this;
		}

		public function invalidate(string $name = null, callable $callback = null): array {
			$snippetList = [];
			foreach (($name ? (array)$name : array_keys($this->snippetList)) as $snippet) {
				if (isset($this->snippetList[$snippet]) === false) {
					throw new ControlException(sprintf('Requested unknown snippet [%s] on control [%s].', $snippet, static::class));
				}
				/** @var $control IControl */
				list(, $parent, $control, $invalidator) = $this->snippetList[$snippet];
				if ($parent === null) {
					continue;
				}
				$callback = $callback ?: $invalidator;
				$callback ? $callback($control) : null;
				if ($control->isDirty()) {
					$snippetList[] = $control;
				}
			}
			return $snippetList;
		}

		public function isDirty(): bool {
			$this->use();
			return $this->node->getMeta('dirty', false);
		}

		public function handle(string $method, array $parameterList, array $crateList) {
			if (method_exists($this, $actionMethod = $method)) {
				$callback = new Callback([
					$this,
					$actionMethod,
				]);
				$argumentCount = count($argumentList = $crateList);
				foreach ($callback->getParameterList() as $parameter) {
					if (--$argumentCount >= 0) {
						continue;
					}
					if (isset($parameterList[$parameter->getName()]) === false) {
						if ($parameter->isOptional()) {
							continue;
						}
						throw new ControlException(sprintf('Missing action parameter [%s::%s(, ...$%s, ...)].', static::class, $actionMethod, $parameter->getName()));
					}
					$argumentList[] = $parameterList[$parameter->getName()];
				}
				return $callback->invoke(...$argumentList);
			}
			/**
			 * ability to process __call methods; the only restriction is execution without parameters
			 */
			return $this->{$actionMethod}();
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
