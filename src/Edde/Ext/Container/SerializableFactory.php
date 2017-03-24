<?php
	declare(strict_types=1);

	namespace Edde\Ext\Container;

	use Edde\Api\Container\IContainer;
	use Edde\Api\Container\IDependency;
	use Edde\Common\Container\AbstractFactory;
	use Edde\Common\Container\Dependency;

	class SerializableFactory extends AbstractFactory {
		/**
		 * @var string
		 */
		protected $name;
		/**
		 * @var object
		 */
		protected $instance;

		/**
		 * Steve and his buddies were hanging out and planning an upcoming fishing trip.
		 * Unfortunately, he had to tell them that he couldn't go this time because his wife wouldn't let him.
		 * After a lot of teasing and name calling, Steve headed home frustrated.
		 *
		 * The following week when Steve's buddies arrived at the lake to set up camp, they were shocked to see Steve.
		 * He was already sitting at the campground with a cold beer, swag rolled out, fishing rod in hand, and a camp fire glowing.
		 * "How did you talk your missus into letting you go Steve?"
		 * "I didn't have to," Steve replied.
		 * "Yesterday, when I left work, I went home and slumped down in my chair with a beer to drown my sorrows because I couldn't go fishing. Then the ol' lady Snuck up behind me and covered my eyes and said, 'Surprise'. When I peeled her hands back, she was standing there in a beautiful see through negligee and she said, 'Carry me into the bedroom, tie me to the bed and you can do whatever you want,' So, Here I am!"
		 *
		 * @param string $name
		 * @param object $instance
		 */
		public function __construct(string $name, $instance) {
			$this->name = $name;
			$this->instance = $instance;
		}

		/**
		 * @inheritdoc
		 */
		public function canHandle(IContainer $container, string $dependency): bool {
			return $this->name === $dependency;
		}

		/**
		 * @inheritdoc
		 */
		public function dependency(IContainer $container, string $dependency = null): IDependency {
			return new Dependency();
		}

		/**
		 * @inheritdoc
		 */
		public function execute(IContainer $container, array $parameterList, IDependency $dependency, string $name = null) {
			return $this->instance;
		}
	}
