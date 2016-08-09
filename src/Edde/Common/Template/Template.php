<?php
	declare(strict_types = 1);

	namespace Edde\Common\Template;

	use Edde\Api\Html\IHtmlControl;
	use Edde\Api\Template\ITemplate;
	use Edde\Common\AbstractObject;

	class Template extends AbstractObject implements ITemplate {
		/**
		 * @var IHtmlControl
		 */
		protected $htmlControl;
		/**
		 * @var string
		 */
		protected $file;

		/**
		 * @param IHtmlControl $htmlControl
		 * @param string $file
		 */
		public function __construct(IHtmlControl $htmlControl, string $file) {
			$this->htmlControl = $htmlControl;
			$this->file = $file;
		}

		public function getControl(): IHtmlControl {
			return $this->htmlControl;
		}

		public function getFile(): string {
			return $this->file;
		}
	}
