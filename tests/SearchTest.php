<?php
declare (strict_types = 1);

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * this will test all API for the applications
 *
 */
final class SearchTest extends TestCase {

	private $client;
	private $api_url;
	private $item_id;
	private $dynmic;
	const API_GET = 'GET';
    const API_POST = 'POST';
	const API_PUT = 'PUT';
	
	/**
	 * this will ceate randomString
	 *
	 * @param [type] $n
	 * @return void
	 */
	private function get_ran_text($n) { 
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
		$randomString = ''; 
	  
		for ($i = 0; $i < $n; $i++) { 
			$index = rand(0, strlen($characters) - 1); 
			$randomString .= $characters[$index]; 
		} 
	  
		return $randomString; 
	} 

	/**
	 * setup for tests
	 *
	 * @return void
	 */
	public function setUp() {
		$this->dynmic = $this->get_ran_text(10);
		$this->api_url = 'http://localhost/api/v1/';
		$this->client = new Client([
			'base_uri' => $this->api_url,
			'timeout'  => 2.0,
			]);
	}

	/**
	 * close down the test
	 *
	 * @return void
	 */
	public function tearDown() {
		$this->client = null;
	}

	/**
	 * test listings API
	 */
	public function testGet_Listings() {
		$response = $this->client->Request('GET', 'items');
		
		$this->assertEquals(200, $response->getStatusCode());
		$body = (string) $response->getBody();
		$data = json_decode($body, true);
		$this->assertArrayHasKey('data', $data);
	}

	/**
	 * test listings API with parameters
	 *
	 * @return void
	 */
	public function testGet_Parameterise_Listings() {
		$response = $this->client->Request('GET', 'items', [
			'json' => ['rating' => '3']
		]);
		
		$this->assertEquals(200, $response->getStatusCode());
		$body = (string) $response->getBody();
		$data = json_decode($body, true);
		$this->assertArrayHasKey('data', $data);
	}

	/**
	 * this will test item API
	 */
	public function testGet_Item() {
		$response = $this->client->Request('GET', 'item/1');
		
		$this->assertEquals(200, $response->getStatusCode());
		$body = (string) $response->getBody();
		$data = json_decode($body, true);
		$this->assertArrayHasKey('data', $data);
	}

	/**
	 * this will test create item API
	 *
	 * @return void
	 */
	public function testPost_Create_Item()
	{
		$response = $this->client->Request('POST', 'item', ['http_errors' => true, 'json' => [
			"name" =>  "Trivago' . $this->dynmic . ' Hotel",
			"rating" =>  "4",
			"category" =>  "hotel",
			"location" =>  [
			  "city" =>  "Berlin",
			  "state" =>  "Berlin",
			  "country" =>  "Germany",
			  "zip_code" =>  "10045",
			  "address" =>  "Invalide nstrasse 31 , 10115, Berlin, Deutschland"
			],
			"image" =>  "36400462.webp",
			"reputation" =>  "836",
			"price" =>  "70.00",
			"availability" =>  "50"
		  ]
		  ]);

		$this->assertEquals(200, $response->getStatusCode());

		$body = (string) $response->getBody();
		$data = json_decode($body, true);

		$this->item_id = $data['data']['id'];
		$this->assertArrayHasKey('status', $data);
		$this->assertEquals('Item successfully saved', $data['messages']);
	}

	/**
	 * this will test update item API
	 *
	 * @return void
	 */
	public function testPut_Update_Item()
	{
		$response = $this->client->Request('PUT', 'item', ['http_errors' => true, 'json' => [			
				"id" =>  $this->item_id,	
				"name" =>  "Boutique '. $this->dynmic .'Hotel i31",
				"rating" =>  "5",
				"category" =>  "hotel",
				"location" =>  [
				  "city" =>  "Berlin",
				  "state" =>  "Berlin",
				  "country" =>  "Germany",
				  "zip_code" =>  "10115",
				  "address" =>  "Invalidenstrasse 31 , 10115, Berlin, Deutschland"
				],
				"image" =>  "36400462.webp",
				"reputation" =>  "1000",
				"price" =>  "100.00",
				"availability" =>  "10"			  
		]
		]);

		$this->assertEquals(200, $response->getStatusCode());
	}

	/**
	 * this will test create booking API
	 *
	 * @return void
	 */
	public function testPost_Create_Book()
	{
		$response = $this->client->Request('POST', 'book', ['http_errors' => true, 'json' => [
			'id' => $this->item_id
		]
		]);

		$this->assertEquals(200, $response->getStatusCode());
	}

	/**
	 * this will test delete API failure
	 */
	public function testDelete_Item_Fail()
	{
		$response = $this->client->Request('DELETE', 'item', ['http_errors' => true, 'json' => ['id' => '-1']]);
		$body = (string) $response->getBody();
		$data = json_decode($body, true);

		$this->assertEquals(404, $data['status']);
	}

	/**
	 * this will test delete item API
	 */
	public function testDelete_Item()
	{
		$response = $this->client->Request('DELETE', 'item', ['http_errors' => true, 'json' => ['id' => $this->item_id]]);
		$body = (string) $response->getBody();
		$data = json_decode($body, true);

		$this->assertEquals(404, $data['status']);
	}
	
}