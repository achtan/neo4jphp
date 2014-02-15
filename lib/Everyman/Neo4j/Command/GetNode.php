<?php
namespace Everyman\Neo4j\Command;

use Everyman\Neo4j\Command,
	Everyman\Neo4j\Client,
	Everyman\Neo4j\Exception,
	Everyman\Neo4j\Node;

/**
 * Get and populate a node
 */
class GetNode extends Command
{
	protected $node = null;
	protected $nodeId = null;

	/**
	 * Set the node to drive the command
	 *
	 * @param Client $client
	 * @param Node|int $nodeOrId
	 */
	public function __construct(Client $client, $nodeOrId)
	{
		parent::__construct($client);
		if($nodeOrId instanceof Node) {
			$this->node = $nodeOrId;
			$nodeOrId->hasId() && $this->nodeId = $nodeOrId->getId();
		} else {
			$this->nodeId = $nodeOrId;
		}
	}

	/**
	 * Return the data to pass
	 *
	 * @return mixed
	 */
	protected function getData()
	{
		return null;
	}

	/**
	 * Return the transport method to call
	 *
	 * @return string
	 */
	protected function getMethod()
	{
		return 'get';
	}

	/**
	 * Return the path to use
	 *
	 * @return string
	 */
	protected function getPath()
	{
		if($this->nodeId === NULL) {
			throw new Exception('No node id specified');
		}

		return '/node/'.$this->nodeId;

	}

	/**
	 * Use the results
	 *
	 * @param integer $code
	 * @param array   $headers
	 * @param array   $data
	 * @return boolean true on success
	 * @throws Exception on failure
	 */
	protected function handleResult($code, $headers, $data)
	{
		if ((int)($code / 100) == 2) {
			if($this->node) {
				$node = $this->node;
			} else {
				$node = $this->client->makeNode($data['data']);
				$node->setId($this->nodeId);
			}

			$node = $this->getEntityMapper()->populateNode($node, $data);
			$this->getEntityCache()->setCachedEntity($node);

			if($this->node) {
				$this->node = $node;
				return true;
			} else {
				return $node;
			}
		} else {
			$this->throwException('Unable to retrieve node', $code, $headers, $data);
		}
	}
}
