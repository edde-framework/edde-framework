<?php
	declare(strict_types=1);

	namespace Edde\Ext\Protocol;

	use Edde\Api\Node\INode;
	use Edde\Api\Protocol\IElement;
	use Edde\Common\Application\Response;

	class ElementResponse extends Response {
		public function __construct(IElement $element, array $targetList = null) {
			parent::__construct($element, INode::class, $targetList);
		}
	}
