<?php
	declare(strict_types=1);

	namespace Edde\Common\Template;

	use Edde\Api\Node\INode;
	use Edde\Api\Node\ITreeTraversal;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\Node\AbstractTreeTraversal;

	abstract class AbstractMacro extends AbstractTreeTraversal implements IMacro {
		const EVENT_PRE_ENTER = 0;
		const EVENT_POST_ENTER = 1;
		const EVENT_PRE_NODE = 2;
		const EVENT_POST_NODE = 3;
		const EVENT_PRE_LEAVE = 4;
		const EVENT_POST_LEAVE = 5;
		/**
		 * @var callable[]
		 */
		protected $eventList = [];

		/**
		 * @inheritdoc
		 */
		public function inline(IMacro $source, ITemplate $template, \Iterator $iterator, INode $node, $value = null) {
		}

		/**
		 * @inheritdoc
		 */
		public function event($event, callable $callback): IMacro {
			$this->eventList[$event][] = $callback;
			return $this;
		}

		protected function call($event) {
			foreach ($this->eventList[$event] ?? [] as $callable) {
				$callable();
			}
		}

		/**
		 * @inheritdoc
		 */
		public function traverse(INode $node, ...$parameters): ITreeTraversal {
			/** @var $template ITemplate */
			list($template) = $parameters;
			return $template->getMacro($node->getName(), $node);
		}

		/**
		 * @inheritdoc
		 */
		public function enter(INode $node, \Iterator $iterator, ...$parameters) {
			$this->eventList = [];
			/** @var $template ITemplate */
			list($template) = $parameters;
			$attributeList = $node->getAttributeList();
			$inlineList = $attributeList->get('t', []);
			$attributeList->remove('t');
			foreach ($inlineList as $name => $value) {
				$macro = $template->getMacro($name, $node);
				$macro->inline($this, $template, $iterator, $node, $value);
			}
			$this->call(self::EVENT_PRE_ENTER);
			$this->onEnter($node, $iterator, ...$parameters);
			$this->call(self::EVENT_POST_ENTER);
		}

		/**
		 * @inheritdoc
		 */
		public function node(INode $node, \Iterator $iterator, ...$parameters) {
			$this->call(self::EVENT_PRE_NODE);
			$this->onNode($node, $iterator, ...$parameters);
			$this->call(self::EVENT_POST_NODE);
		}

		/**
		 * @inheritdoc
		 */
		public function leave(INode $node, \Iterator $iterator, ...$parameters) {
			$this->call(self::EVENT_PRE_LEAVE);
			$this->onLeave($node, $iterator, ...$parameters);
			$this->call(self::EVENT_POST_LEAVE);
		}

		/**
		 * @inheritdoc
		 */
		public function register(ITemplate $template): IMacro {
			foreach ($this->getNameList() as $name) {
				$template->registerMacro($name, $this);
			}
			return $this;
		}

		protected function onEnter(INode $node, \Iterator $iterator, ...$parameters) {
		}

		protected function onNode(INode $node, \Iterator $iterator, ...$parameters) {
			parent::node($node, $iterator, ...$parameters);
		}

		protected function onLeave(INode $node, \Iterator $iterator, ...$parameters) {
		}
	}
