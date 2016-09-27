<?php
	declare(strict_types = 1);

	namespace Edde\Common\Translator;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Translator\ITranslator;
	use Edde\Api\Translator\TranslatorException;
	use Edde\Common\Translator\Dictionary\CsvDictionary;
	use Edde\Ext\Container\ContainerFactory;
	use Foo\Bar\DummyDictionary;
	use Foo\Bar\EmptyDictionary;
	use phpunit\framework\TestCase;

	require_once(__DIR__ . '/assets/assets.php');

	class TranslatorTest extends TestCase {
		/**
		 * @var IContainer
		 */
		protected $container;
		/**
		 * @var ITranslator
		 */
		protected $translator;

		public function testUseException() {
			$this->expectException(TranslatorException::class);
			$this->expectExceptionMessage('Cannot use translator without set language.');
			$this->translator->use();
		}

		public function testWithoutDictionaryException() {
			$this->expectException(TranslatorException::class);
			$this->expectExceptionMessage('Translator needs at least one dictionary. Or The God will kill one cute devil kitten!');
			$this->translator->onDeffered(function (ITranslator $translator) {
				$translator->setLanguage('en');
			});
			$this->translator->use();
		}

		public function testEmptyDictionaryException() {
			$this->expectException(TranslatorException::class);
			$this->expectExceptionMessage('Cannot translate [foo]; the given id is not available in no dictionary.');
			$this->translator->registerDictionary(new EmptyDictionary());
			$this->translator->onDeffered(function (ITranslator $translator) {
				$translator->setLanguage('en');
			});
			$this->translator->translate('foo');
		}

		public function testDummyDictionary() {
			$this->translator->registerDictionary(new EmptyDictionary());
			$this->translator->registerDictionary(new DummyDictionary());
			$this->translator->onDeffered(function (ITranslator $translator) {
				$translator->setLanguage('en');
			});
			self::assertEquals('foo.en', $this->translator->translate('foo'));
		}

		public function testCsvDictionary() {
			$this->translator->registerDictionary($csvDictionary = $this->container->create(CsvDictionary::class));
			$csvDictionary->addFile(__DIR__ . '/assets/en.csv');
			$csvDictionary->addFile(__DIR__ . '/assets/cs.csv');
			$this->translator->setLanguage('en');
			self::assertEquals('english foo', $this->translator->translate('foo'));
			self::assertEquals('czech foo', $this->translator->translate('foo', [], 'cs'));
		}

		public function testDictionaryParameters() {
			$this->translator->registerDictionary($csvDictionary = $this->container->create(CsvDictionary::class));
			$csvDictionary->addFile(__DIR__ . '/assets/dic.csv');
			$this->translator->setLanguage('en');
			self::assertEquals('english some param foo fooooped foo', $this->translator->translate('foo', $parameters = [
				'param' => 'some param',
				'foo' => 'fooooped foo',
			]));
			self::assertEquals('czech foo some paramfooooped foo', $this->translator->translate('foo', $parameters, 'cs'));
		}

		public function testDictionaryParameterf() {
			$this->translator->registerDictionary($csvDictionary = $this->container->create(CsvDictionary::class));
			$csvDictionary->addFile(__DIR__ . '/assets/dic.csv');
			$this->translator->setLanguage('en');
			self::assertEquals('english "some param" foo 3.142', $this->translator->translatef('foof', $parameters = [
				'"some param"',
				3.14156,
			]));
			self::assertEquals('czech foo "some param" 3.142', $this->translator->translatef('foof', $parameters, 'cs'));
		}

		protected function setUp() {
			$this->container = $container = ContainerFactory::create([
				ITranslator::class => Translator::class,
			]);
			$this->translator = new Translator();
		}
	}
