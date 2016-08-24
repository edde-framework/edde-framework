<?php
	declare(strict_types = 1);

	namespace Edde\Common\Html;

	use Edde\Api\Link\ILinkGenerator;
	use Edde\Common\Container\LazyInjectTrait;

	class ButtonControl extends AbstractHtmlControl {
		use LazyInjectTrait;

		/**
		 * @var ILinkGenerator
		 */
		protected $linkGenerator;

		public function lazyLinkGenerator(ILinkGenerator $linkGenerator) {
			$this->linkGenerator = $linkGenerator;
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
			$this->setAttribute('data-action', $this->linkGenerator->generate($action));
			return $this;
		}

		protected function prepare() {
			parent::prepare();
			$this->setTag('div', true);
			$this->addClass('button edde-clickable');
		}
	}
