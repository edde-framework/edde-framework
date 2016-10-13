<?php
	declare(strict_types = 1);

	namespace Edde\Common\Container;

	use Edde\Api\Container\IDependency;
	use Edde\Api\Node\IAbstractNode;
	use Edde\Common\Node\Node;

	/**
	 * DI dependency definition node.
	 */
	class Dependency extends Node implements IDependency {
		/**
		 * A computer programmer happens across a frog in the road. The frog gets excited and shouts, "I'm really a beautiful princess and if you kiss me, I'll do anything you want for a whole week".
		 *
		 * The programmer shrugs his shoulders and puts the frog in his pocket. A few minutes later, the frog says "OK, OK, if you kiss me, I'll give you great sex for a month". The programmer nods and puts the frog back in his pocket.
		 *
		 * A few minutes later, "Turn me back into a princess and I'll give you a great sex for a whole year!". The programmer smiles and walks on.
		 *
		 * Finally, the frog says, "What's wrong with you? I've promised you great sex for a year from a beautiful princess and you won't even kiss a frog?"
		 *
		 * "I'm a programmer," he replies. "I don't have any interest in great sex.... but a talking frog is pretty cool."
		 *
		 * @param string $name
		 * @param bool $mandatory mandatory means needed in constructor
		 * @param bool $optional optional can be optional, including constructor
		 * @param string $class
		 */
		public function __construct(string $name, bool $mandatory, bool $optional, string $class = null) {
			parent::__construct($name, null, [
				'mandatory' => $mandatory,
				'optional' => $optional,
				'class' => $class,
			]);
		}

		public function hasClass(): bool {
			return $this->getClass() !== null;
		}

		public function getClass() {
			return $this->getAttribute('class');
		}

		public function isOptional(): bool {
			return $this->getAttribute('optional', false) === true;
		}

		/**
		 * return dependency list of this dependency
		 *
		 * @return IDependency[]
		 */
		public function getDependencyList(): array {
			return $this->getNodeList();
		}

		/** @noinspection PhpMissingParentCallCommonInspection */
		/**
		 * @inheritdoc
		 */
		public function accept(IAbstractNode $abstractNode) {
			return $abstractNode instanceof IDependency;
		}
	}
