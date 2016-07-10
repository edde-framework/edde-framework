<?php
	namespace Edde\Common\Query;

	use Edde\Api\Query\IStaticQuery;
	use Edde\Common\AbstractObject;

	class StaticQuery extends AbstractObject implements IStaticQuery {
		/**
		 * @var mixed
		 */
		private $query;
		/**
		 * @var array
		 */
		private $parameterList;

		/**
		 * @param mixed $query
		 * @param array $parameterList
		 */
		public function __construct($query, array $parameterList = []) {
			$this->query = $query;
			$this->parameterList = $parameterList;
		}

		public function getQuery() {
			return $this->query;
		}

		public function hasParameterList() {
			return empty($this->parameterList) === false;
		}

		public function getParameterList() {
			return $this->parameterList;
		}
	}
