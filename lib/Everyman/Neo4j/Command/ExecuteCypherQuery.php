<?php
namespace Everyman\Neo4j\Command;

use Everyman\Neo4j\EntityMapper,
    Everyman\Neo4j\Command,
	Everyman\Neo4j\Client,
	Everyman\Neo4j\Cypher\ResultSet;

class ExecuteCypherQuery extends Command
{
	protected $query = null;
	protected $entityMapper = null;

	protected $results = array();

	/**
	 * Set the query to execute
	 *
	 * @param Client $client
	 * @param EntityMapper $entityMapper
	 * @param query $query
	 */
	public function __construct(Client $client, EntityMapper $entityMapper, $query)
	{
		parent::__construct($client);
		$this->entityMapper = $entityMapper;
		$this->query = $query;
	}

	/**
	 * Return the data to pass
	 *
	 * @return mixed
	 */
	protected function getData()
	{
		return array('query'=>$this->query);
	}

	/**
	 * Return the transport method to call
	 *
	 * @return string
	 */
	protected function getMethod()
	{
		return 'post';
	}

	/**
	 * Return the path to use
	 *
	 * @return string
	 */
	protected function getPath()
	{
		return '/ext/CypherPlugin/graphdb/execute_query';
	}

	/**
	 * Get the result array of entities
	 *
	 * @return array
	 */
	public function getResult()
	{
		return $this->results;
	}

	/**
	 * Use the results
	 *
	 * @param integer $code
	 * @param array   $headers
	 * @param array   $data
	 * @return integer on failure
	 */
	protected function handleResult($code, $headers, $data)
	{
		if ((int)($code / 100) == 2) {
			$this->results = new ResultSet($this->client, $this->entityMapper, $data);
			return null;
		}
		return $code;
	}
}

