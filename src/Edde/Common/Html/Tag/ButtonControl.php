<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html\Tag;

	use Edde\Api\Link\ILinkFactory;
	use Edde\Common\Container\LazyInjectTrait;
	use Edde\Common\Html\AbstractHtmlControl;

	class ButtonControl extends AbstractHtmlControl {
		use LazyInjectTrait;

		/**
		 * @var ILinkFactory
		 */
		protected $linkFactory;

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

		public function setHint(string $hint) {
			$this->setAttribute('hint', $hint);
			return $this;
		}

		public function setAction($action) {
			$this->setAttribute('data-action', $this->linkFactory->generate($action));
			return $this;
		}

		protected function prepare() {
			parent::prepare()
				->javascript()
				->setTag('div', true)
				->addClass('button');
		}
	}
