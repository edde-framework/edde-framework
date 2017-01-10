<?php
	declare(strict_types = 1);

	namespace Edde\Ext\Stream;

	use Edde\Api\Stream\IConnection;
	use Edde\Api\Stream\IConnectionHandler;
	use Edde\Common\Stream\AbstractConnectionHandler;
	use FG\ASN1\Object;

	class LdapConnectionHandler extends AbstractConnectionHandler {
		public function read(IConnection $connection): IConnectionHandler {
			$source = $connection->read();
			$asn = Object::fromBinary($source);
			// $asn[2]->getContent()[0][0]->getBinaryContent()
			// MEECAQFgHQIBAwQRY249c2FmZXEsZGM9c2FmZXGABXNhZmVxoB0wGwQZMS4zLjYuMS40LjEuNDIuMi4yNy44LjUuMQ==
			// MCQCAQJCAKAdMBsEGTEuMy42LjEuNC4xLjQyLjIuMjcuOC41LjE= (1234568)
			return $this;
		}
	}
