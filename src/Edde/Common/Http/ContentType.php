<?php
	declare(strict_types=1);

	namespace Edde\Common\Http;

	use Edde\Api\Http\IContentType;
	use Edde\Common\Collection\AbstractList;
	use Edde\Common\Config\ConfigurableTrait;

	class ContentType extends AbstractList implements IContentType {
		use ConfigurableTrait;
		/**
		 * source content type
		 *
		 * @var string
		 */
		protected $contentType;
		/**
		 * parsed content type
		 *
		 * @var \stdClass
		 */
		protected $object;

		/**
		 * ContentType constructor.
		 *
		 * @param string $contentType can be only content type part or whole content type header
		 */
		public function __construct(string $contentType) {
			parent::__construct();
			$this->contentType = $contentType;
		}

		public function getCharset(string $default = 'utf-8'): string {
			return $this->get('charset', $default);
		}

		public function getMime(string $default = null) {
			return $this->object ? $this->object->mime : $default;
		}

		public function getParameterList(): array {
			return $this->array();
		}

		public function handleInit() {
			parent::handleInit();
			if ($this->contentType) {
				$this->object = HttpUtils::contentType($this->contentType);
				$this->put($this->object->params);
			}
		}

		public function __toString(): string {
			return $this->getMime();
		}
	}
