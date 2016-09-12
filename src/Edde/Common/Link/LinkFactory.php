<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Link\ILinkGenerator;
	use Edde\Api\Link\LinkException;
	use Edde\Common\Usable\AbstractUsable;

	class LinkFactory extends AbstractUsable implements ILinkFactory {
		/**
		 * @var \Edde\Api\Http\IHostUrl
		 */
		protected $hostUrl;
		/**
		 * @var ILinkGenerator[]
		 */
		protected $linkGeneratorList = [];

		/**
		 * @param \Edde\Api\Http\IHostUrl $hostUrl
		 */
		public function __construct(\Edde\Api\Http\IHostUrl $hostUrl) {
			$this->hostUrl = $hostUrl;
		}

		public function registerLinkGenerator(ILinkGenerator $lingGenerator): ILinkFactory {
			$this->linkGeneratorList[] = $lingGenerator;
			return $this;
		}

		public function generate($generate, ...$parameterList) {
			$this->use();
			foreach ($this->linkGeneratorList as $linkGenerator) {
				if (($url = $linkGenerator->generate($generate, ...$parameterList)) !== null) {
					return $url;
				}
			}
			throw new LinkException(sprintf('Cannot generate link from the given input.'));
		}

		protected function prepare() {
		}
	}
