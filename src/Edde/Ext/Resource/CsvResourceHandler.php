<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Resource;

	use Edde\Api\Node\INode;
	use Edde\Api\Resource\IResource;
	use Edde\Common\Node\Node;
	use Edde\Common\Resource\AbstractResourceHandler;

	class CsvResourceHandler extends AbstractResourceHandler {
		public function getMimeTypeList(): array {
			return [
				'text/csv',
			];
		}

		public function handle(IResource $resource): INode {
			$root = new Node();
//			$handler = fopen($resource->getUrl()
//				->getAbsoluteUrl(), 'r');
//			while($line = fgetcsv($handler))
//			fclose($handler);
			return $root;
		}
	}
