<?php
	declare(strict_types=1);

	namespace Edde\Common\Control;

	use Edde\Api\Application\IRequest;
	use Edde\Api\Application\IResponse;
	use Edde\Api\Callback\ICallback;
	use Edde\Api\Config\IConfigurable;
	use Edde\Api\Control\ControlException;
	use Edde\Api\Control\IControl;
	use Edde\Api\Node\INode;
	use Edde\Api\Node\NodeException;
	use Edde\Common\Callback\Callback;
	use Edde\Common\Config\ConfigurableTrait;
	use Edde\Common\Node\Node;
	use Edde\Common\Node\NodeIterator;
	use Edde\Common\Object;
	use Edde\Common\Strings\StringUtils;

	/**
	 * Root implementation of all controls.
	 */
	abstract class AbstractControl extends Object implements IConfigurable, IControl {
		use ConfigurableTrait;
		/**
		 * @var INode
		 */
		protected $node;
		/**
		 * When control is called by execute, the request is saved here for future reference; this basically
		 * means that control cannot be reused, because it will loose reference to the original request.
		 *
		 * @var IRequest
		 */
		protected $request;

		/**
		 * @return INode
		 */
		public function getNode(): INode {
			return $this->node;
		}

		/**
		 * @inheritdoc
		 */
		public function addControl(IControl $control): IControl {
			$this->node->addNode($control->getNode(), true);
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function addControlList(array $controlList): IControl {
			foreach ($controlList as $control) {
				$this->addControl($control);
			}
			return $this;
		}

		/**
		 * @inheritdoc
		 */
		public function getControlList(): array {
			$controlList = [];
			foreach ($this->node->getNodeList() as $node) {
				$controlList[] = $node->getMeta('control');
			}
			return $controlList;
		}

		/**
		 * @inheritdoc
		 */
		public function request(IRequest $request): IResponse {
			return $this->execute($request->getAction(), $this->request = $request);
		}

		protected function execute(string $method, IRequest $request) {
			$argumentList = array_filter($parameterList = $request->getParameterList(), function ($key) {
				return is_int($key);
			}, ARRAY_FILTER_USE_KEY);
			if (method_exists($this, $method)) {
				/** @var $callback ICallback */
				$callback = new Callback([
					$this,
					$method,
				]);
				$argumentCount = count($argumentList);
				foreach ($callback->getParameterList() as $key => $parameter) {
					if (--$argumentCount >= 0) {
						continue;
					}
					if (isset($parameterList[$parameterName = StringUtils::recamel($parameter->getName())]) === false) {
						if ($parameter->isOptional()) {
							continue;
						}
						throw new MissingActionParameterException(sprintf('Missing action parameter [%s::%s(, ...$%s, ...)].', static::class, $method, $parameter->getName()));
					}
					$argumentList[] = $parameterList[$parameterName];
				}
				return $callback->invoke(...$argumentList);
			}
			return $this->action(StringUtils::recamel($method), $argumentList);
		}

		/**
		 * @inheritdoc
		 */
		public function getAction(): string {
			return StringUtils::recamel($this->request->getAction());
		}

		/**
		 * @inheritdoc
		 */
		public function getContent(string $target = 'array') {
			return $this->request->getContent([$target]);
		}

		/**
		 * when handle method does not exists, this generic method will be executed
		 *
		 * @param string $action
		 * @param array  $parameterList
		 *
		 * @throws ControlException
		 */
		protected function action(string $action, array $parameterList) {
			throw new UnknownActionException(sprintf('Unknown handle method [%s]; to disable this exception, override [%s::%s()] method or implement [%s::%s()].', $action, static::class, __FUNCTION__, static::class, StringUtils::toCamelHump($action)));
		}

		/**
		 * @inheritdoc
		 * @throws NodeException
		 */
		public function traverse(bool $self = true) {
			foreach (NodeIterator::recursive($this->node, $self) as $node) {
				yield $node->getMeta('control');
			}
		}

		/**
		 * @inheritdoc
		 */
		protected function handleInit() {
			parent::handleInit();
			$this->node = new Node();
			$this->node->setMeta('control', $this);
		}
	}
