<?php
// requires php-redis ext
// requires $mysqli instance e.g. new DbRedis($mysqli)

class DbRedis extends Redis
{
	private $redis;
	private $mysqli;

	function __construct($mysqli)
	{
		// $this->redis = new Redis();
		parent::__construct();
		$this->mysqli = $mysqli;
		$this->redis = $this;
		$this->redis->connect('redis', 6379);
	}

	public function getRedisInstance()
	{
		return $this->redis;
	}

	public function getCachedQueryResultRedis($cacheKey, $query, $expiry = 300)
	{
		// Check if data is in cache
		if ($this->redis->exists($cacheKey)) {
			return json_decode($this->redis->get($cacheKey), true);
		}

		// Execute the query
		$result = $this->mysqli->query($query);

		if (!$result) {
			return false;
		}

		$data = $result->fetch_all(MYSQLI_ASSOC);

		// Store in Redis
		$this->redis->setex($cacheKey, $expiry, json_encode($data));

		return $data;
	}


	public function setCachedData($cacheKey, $data, $expiry = 300)
	{
		$this->redis->setex($cacheKey, $expiry, json_encode($data));
	}

	public function getCachedData($cacheKey)
	{
		// Check if data is in cache
		if ($this->redis->exists($cacheKey)) {
			return json_decode($this->redis->get($cacheKey), true);
		}
		return null;
	}
}
