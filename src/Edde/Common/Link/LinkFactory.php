<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Link\IHostUrl;
	use Edde\Api\Link\ILingGenerator;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Link\LinkException;
	use Edde\Common\AbstractObject;

	class LinkFactory extends AbstractObject implements ILinkFactory {
		/**
		 * @var IHostUrl
		 */
		protected $hostUrl;
		/**
		 * @var ILingGenerator[]
		 */
		protected $linkGeneratorList = [];

		/**
		 * @param IHostUrl $hostUrl
		 */
		public function __construct(IHostUrl $hostUrl) {
			$this->hostUrl = $hostUrl;
		}

		public function registerLinkGenerator(ILingGenerator $lingGenerator): ILinkFactory {
			$this->linkGeneratorList[] = $lingGenerator;
			return $this;
		}

		public function generate($generate, ...$parameterList) {
			foreach ($this->linkGeneratorList as $lingGenerator) {
				if (($url = $lingGenerator->generate($generate, ...$parameterList)) !== null) {
					return $url;
				}
			}
			throw new LinkException(sprintf('Cannot generate link from the given input.'));
		}
	}
