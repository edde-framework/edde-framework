<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Node\INode;
	use Edde\Api\Template\ICompiler;
	use Edde\Api\Template\IMacro;
	use Edde\Api\Template\MacroException;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Html\AbstractHtmlControl;
	use Edde\Common\Template\Macro\Control\ControlMacro;

	class ButtonControl extends AbstractHtmlControl {
		use LazyInjectTrait;

		/**
		 * @var ILinkFactory
		 */
		protected $linkFactory;

		static public function macro(): IMacro {
			return new class extends ControlMacro {
				public function __construct() {
					parent::__construct(['button'], ButtonControl::class);
				}

				public function run(INode $root, ICompiler $compiler, callable $callback = null) {
					$destination = $compiler->getDestination();
					$destination->write("\t\t\t\$parent = \$this->stack->top();\n");
					$destination->write(sprintf("\t\t\t\$parent->addControl(\$control = \$this->container->create('%s'));\n", $this->control));
					$attributeList = $this->getAttributeList($root, $compiler);
					if (isset($attributeList['action']) === false) {
						throw new MacroException(sprintf('Missing mandatory attribute "action" in [%s].', $root->getPath()));
					}
					$action = $root->getAttribute('action');
					unset($attributeList['action']);
					$destination->write(sprintf("\t\t\t\$control->setAction([\$this->proxy, %s]);\n", $compiler->value($action)));
					$this->writeAttributeList($attributeList, $destination);
					$this->macro($root, $compiler, $callback);
				}
			};
		}

		public function lazyLinkFactory(ILinkFactory $linkFactory) {
			$this->linkFactory = $linkFactory;
		}

		public function setTitle($title) {
			$this->use();
			$this->setAttribute('title', $title);
			return $this;
		}

		public function setAttribute($attribute, $value) {
			$this->use();
			switch ($attribute) {
				case 'value':
					$this->node->setValue($value);
					break;
				case 'title':
					$this->node->setAttribute('title', $value);
					break;
				case 'bind':
					$this->node->setAttribute('data-bind', $value);
					break;
				default:
					parent::setAttribute($attribute, $value);
			}
			return $this;
		}

		public function setHint($hint) {
			$this->setAttribute('hint', $hint);
			return $this;
		}

		public function setAction($action) {
			$this->setAttribute('data-action', $this->linkFactory->generate($action));
			return $this;
		}

		protected function prepare() {
			parent::prepare();
			$this->setTag('div', true);
			$this->addClass('button edde-clickable');
		}
	}
