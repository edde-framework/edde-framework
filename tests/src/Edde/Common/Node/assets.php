<?php
	namespace Edde\Common\Node;

	use Edde\Api\Node\IAbstractNode;

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class AlphaNode extends Node {
		public function accept(IAbstractNode $abstractNode) {
			return $abstractNode instanceof AlphaNode;
		}
	}

	/** @noinspection PhpMultipleClassesDeclarationsInOneFile */
	class BetaNode extends Node {
		public function accept(IAbstractNode $abstractNode) {
			return $abstractNode instanceof BetaNode;
		}
	}
