<?php
	declare(strict_types=1);

	namespace Edde\Ext\Database\Sqlite;

	use Edde\Api\Asset\LazyAssetDirectoryTrait;
	use Edde\Api\Cache\ICacheable;
	use Edde\Common\Database\AbstractDsn;

	class SqliteDsn extends AbstractDsn implements ICacheable {
		use LazyAssetDirectoryTrait;

		public function __construct(string $filename, array $optionList = []) {
			parent::__construct($filename, $optionList);
		}

		public function getDsn(): string {
			return 'sqlite:' . $this->assetDirectory->filename($this->dsn);
		}
	}
