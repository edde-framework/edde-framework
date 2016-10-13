<?php
	declare(strict_types = 1);

	namespace Edde\Common\Link;

	use Edde\Api\Http\IHostUrl;
	use Edde\Api\Link\ILink;
	use Edde\Api\Link\ILinkFactory;
	use Edde\Api\Link\ILinkGenerator;
	use Edde\Api\Link\LinkException;
	use Edde\Common\Deffered\AbstractDeffered;

	class LinkFactory extends AbstractDeffered implements ILinkFactory {
		/**
		 * @var IHostUrl
		 */
		protected $hostUrl;
		/**
		 * @var ILinkGenerator[]
		 */
		protected $linkGeneratorList = [];

		/**
		 * @param IHostUrl $hostUrl
		 */
		public function __construct(IHostUrl $hostUrl) {
			$this->hostUrl = $hostUrl;
		}

		/**
		 * @inheritdoc
		 */
		public function registerLinkGenerator(ILinkGenerator $linkGenerator): ILinkFactory {
			$this->linkGeneratorList[] = $linkGenerator;
			return $this;
		}

		/**
		 * @inheritdoc
		 * @throws LinkException
		 */
		public function link($generate, ...$parameterList) {
			$this->use();
			if ($generate instanceof ILink) {
				$parameterList = array_merge($generate->getParameterList(), $parameterList);
				$generate = $generate->getLink();
			}
			foreach ($this->linkGeneratorList as $linkGenerator) {
				if (($url = $linkGenerator->link($generate, ...$parameterList)) !== null) {
					return $url;
				}
			}
			throw new LinkException(sprintf('Cannot generate link from the given input%s.', (is_string($generate) ? ' [' . $generate . ']' : '')));
		}
	}
