<?php
	declare(strict_types = 1);

	namespace Edde\Common\Crate;

	use Edde\Api\Cache\ICache;
	use Edde\Api\Cache\ICacheFactory;
	use Edde\Api\Container\IFactoryManager;
	use Edde\Api\Crate\ICollection;
	use Edde\Api\Crate\ICrateDirectory;
	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Api\Resource\IResource;
	use Edde\Api\Schema\ISchema;
	use Edde\Api\Schema\ISchemaCollection;
	use Edde\Api\Schema\ISchemaLink;
	use Edde\Api\Schema\ISchemaManager;
	use Edde\Api\Schema\ISchemaProperty;
	use Edde\Common\File\FileUtils;
	use Edde\Common\Strings\StringUtils;
	use Edde\Common\Usable\AbstractUsable;

	class CrateGenerator extends AbstractUsable implements ICrateGenerator {
		/**
		 * @var ISchemaManager
		 */
		protected $schemaManager;
		/**
		 * @var ICrateDirectory
		 */
		protected $crateDirectory;
		/**
		 * @var ICacheFactory
		 */
		protected $cacheFactory;
		/**
		 * @var IFactoryManager
		 */
		protected $factoryManager;
		/**
		 * @var ISchema[]
		 */
		protected $excludeSchemaList = [];
		/**
		 * @var ICache
		 */
		protected $cache;
		/**
		 * @var string
		 */
		protected $parent;

		/**
		 * @param ISchemaManager $schemaManager
		 * @param ICrateDirectory $crateDirectory
		 * @param ICacheFactory $cacheFactory
		 * @param IFactoryManager $factoryManager
		 */
		public function __construct(ISchemaManager $schemaManager, ICrateDirectory $crateDirectory, ICacheFactory $cacheFactory, IFactoryManager $factoryManager) {
			$this->schemaManager = $schemaManager;
			$this->crateDirectory = $crateDirectory;
			$this->cacheFactory = $cacheFactory;
			$this->factoryManager = $factoryManager;
		}

		public function excludeSchema(ISchema $schema): ICrateGenerator {
			$this->excludeSchemaList[$schema->getSchemaName()] = $schema;
			return $this;
		}

		public function generate(bool $force = false): ICrateGenerator {
			$this->usse();
			if (($crateList = $this->cache->load('crate-list', [])) === [] || $force === true) {
				$this->crateDirectory->purge();
				foreach ($this->schemaManager->getSchemaList() as $schema) {
					$crateList[] = $schemaName = $schema->getSchemaName();
					if (isset($this->excludeSchemaList[$schemaName])) {
						continue;
					}
					FileUtils::createDir($path = FileUtils::normalize($this->crateDirectory->getDirectory() . '/' . $schema->getNamespace()));
					foreach ($this->compile($schema) as $name => $source) {
						file_put_contents($path . '/' . $schema->getName() . '.php', $source);
					}
				}
				$this->cache->save('crate-list', $crateList);
			}
			$loader = $this->crateDirectory->save('loader.php', "<?php
	Edde\\Common\\Autoloader::register(null, __DIR__, false);	
");
			(function (IResource $resource) {
				require_once($resource->getUrl());
			})($loader);
			$this->factoryManager->registerFactoryList($crateList);
			return $this;
		}

		public function compile(ISchema $schema): array {
			$this->usse();
			$sourceList = [];
			$source[] = "<?php\n";
			$source[] = "\tdeclare(strict_types = 1);\n\n";
			if (($namespace = $schema->getNamespace()) !== '') {
				$source[] = "\tnamespace $namespace;\n\n";
			}
			$source[] = sprintf("\tuse %s;\n", ICollection::class);
			$source[] = sprintf("\tuse %s;\n", $this->parent);
			$source[] = "\n";
			$parent = explode('\\', $this->parent);
			$source[] = sprintf("\tclass %s extends %s {\n", $schema->getName(), end($parent));
			foreach ($schema->getPropertyList() as $schemaProperty) {
				$source[] = $this->generateSchemaProperty($schemaProperty);
			}
			foreach ($schema->getCollectionList() as $schemaCollection) {
				$source[] = $this->generateCollection($schemaCollection);
			}
			foreach ($schema->getLinkList() as $schemaLink) {
				$source[] = $this->generateLink($schemaLink);
			}
			$source[] = "\t}\n";
			$sourceList[$schema->getSchemaName()] = implode('', $source);
			return $sourceList;
		}

		protected function generateSchemaProperty(ISchemaProperty $schemaProperty) {
			$source[] = $this->generateGetter($schemaProperty);
			$source[] = $this->generateSetter($schemaProperty);
			$source[] = '';
			return implode("\n", $source);
		}

		protected function generateGetter(ISchemaProperty $schemaProperty) {
			$source[] = "\t\t/**\n";
			$source[] = sprintf("\t\t * @return %s\n", $schemaProperty->getType());
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function get%s(): %s {\n", StringUtils::camelize($schemaProperty->getName()), $schemaProperty->getType());
			$source[] = sprintf("\t\t\treturn \$this->get('%s');\n", $schemaProperty->getName());
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		protected function generateSetter(ISchemaProperty $schemaProperty) {
			$parameter = StringUtils::firstLower($camelized = StringUtils::camelize($propertyName = $schemaProperty->getName()));
			$source[] = "\t\t/**\n";
			$source[] = sprintf("\t\t * @param %s $%s\n", $schemaProperty->getType(), $parameter);
			$source[] = "\t\t * \n";
			$source[] = "\t\t * @return \$this\n";
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function set%s(%s \$%s) {\n", $camelized, $schemaProperty->getType(), $parameter);
			$source[] = sprintf("\t\t\t\$this->set('%s', \$%s);\n", $propertyName, $parameter);
			$source[] = "\t\t\treturn \$this;\n";
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		protected function generateCollection(ISchemaCollection $schemaCollection) {
			$source[] = '';
			$source[] = "\t\t/**\n";
			$source[] = "\t\t * \n";
			$source[] = sprintf("\t\t * @return %s\n", StringUtils::extract(ICollection::class, '\\', -1));
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function collection%s() {\n", StringUtils::camelize($collectionName = $schemaCollection->getName()));
			$source[] = sprintf("\t\t\treturn \$this->collection('%s');\n", $collectionName);
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		protected function generateLink(ISchemaLink $schemaLink) {
			$targetSchemaName = $schemaLink->getTarget()
				->getSchema()
				->getSchemaName();
			$source[] = '';
			$source[] = "\t\t/**\n";
			$source[] = sprintf("\t\t * @return \\%s\n", $targetSchemaName);
			$source[] = "\t\t */\n";
			$source[] = sprintf("\t\tpublic function link%s() {\n", StringUtils::camelize($linkName = $schemaLink->getName()));
			$source[] = sprintf("\t\t\treturn \$this->link('%s');\n", $linkName);
			$source[] = "\t\t}\n";
			$source[] = "\n";
			$source[] = sprintf("\t\tpublic function set%sLink(\\%s \$%s) {\n", StringUtils::camelize($linkName = $schemaLink->getName()), $targetSchemaName, $linkName);
			$source[] = sprintf("\t\t\t\$this->setLink('%s', \$%s);\n", $linkName, $linkName);
			$source[] = sprintf("\t\t\treturn \$this;\n");
			$source[] = "\t\t}\n";
			return implode('', $source);
		}

		protected function prepare() {
			$this->crateDirectory->make();
			$this->cache = $this->cacheFactory->factory(__NAMESPACE__);
			$this->parent = Crate::class;
		}
	}
