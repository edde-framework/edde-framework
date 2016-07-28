<?php
	namespace Edde\Common\Crate;

	use Edde\Api\Crate\ICrateGenerator;
	use Edde\Common\Schema\Property;
	use Edde\Common\Schema\Schema;
	use phpunit\framework\TestCase;

	class CrateGeneratorTest extends TestCase {
		/**
		 * @var ICrateGenerator
		 */
		protected $crateGenerator;

		public function testSimpleCrate() {
			$schema = new Schema('Foo\\Bar\\FooBar');
			$schema->addPropertyList([
				new Property($schema, 'guid', null, true, true, true),
				new Property($schema, 'name'),
				new Property($schema, 'some-long-named-property'),
			]);
			$crateList = $this->crateGenerator->generate($schema);
			foreach ($crateList as $name => $source) {
				file_put_contents(sha1($name) . '.php', $source);
			}
		}

		protected function setUp() {
			$this->crateGenerator = new CrateGenerator();
		}
	}
