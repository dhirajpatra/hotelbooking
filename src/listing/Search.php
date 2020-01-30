<?php

declare (strict_types = 1);

namespace App\listing;

use App\ConnectDb;
use App\listing\inc\Misc;

/**
 * @OA\Info(title="Search Main API", version="0.1")
 */
class Search
{
    private $dbConn;
    private $misc;

    public function __construct()
    {
        $this->misc = new Misc();
        $this->dbConn = ConnectDb::getConnection();
    }

    /**
     * [check_reputation description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    private function check_reputation($value): string
    {
        try {
            $output = '';

            if ($value >= 0 && $value <= 1000) {
                switch ($value) {
                    case ($value <= 500):
                        $output = 'red';
                        break;

                    case ($value >= 798 && $value <= 799):
                        $output = 'yellow';
                        break;

                    default:
                        $output = 'green';
                        break;
                }
            }

            return $output;
        } catch (\Exception $exception) {
            $this->misc->log('Search' . __METHOD__, $exception);
            http_response_code(500);
        }
    }

    /**
     * [reverse_reputation description]
     * @param  [type] $value [description]
     * @return [type]        [description]
     */
    private function reverse_reputation($value): string
    {
        try {
            $output = '';
            $badges = ['red', 'yellow', 'green'];

            if (in_array($value, $badges)) {
                switch ($value) {
                    case ('red'):
                        $output = 500;
                        break;

                    case ('yellow'):
                        $output = 799;
                        break;

                    default:
                        $output = 1000;
                        break;
                }
            }

            return $output;
        } catch (\Exception $exception) {
            $this->misc->log('Search' . __METHOD__, $exception);
            http_response_code(500);
        }
    }

    /**
     * [set_response description]
     * @param [type] $result [description]
     */
    private function set_response($result): string
    {
        try {
            $response = [
                'status' => $result[0],
                'messages' => $result[1],
                'data' => $result[2],
            ];

            return json_encode($response);
        } catch (\Exception $exception) {
            $this->misc->log('Search' . __METHOD__, $exception);
            http_response_code(500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/items[/{criteria_name}/{criteria_value}]",
     *     summary="Get Listings optional parameters",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="url",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="rating",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="city",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="reputation",
     *                     type="integer"
     *                 ),
     *                  @OA\Property(
     *                     property="availability",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="category",
     *                     type="string"
     *                 ),
     *                 example={"id": 10, "name": "Jessica Smith"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function __invoke(): string
    {
        try {
            // default
            $result = [400, 'Invalid request', null];

            $data = [];
            $where_add = null;

            // parameterise search
            $criteria_name = isset($_REQUEST['vars']['criteria_name']) ? $_REQUEST['vars']['criteria_name'] : false;
            $criteria_value = isset($_REQUEST['vars']['criteria_value']) ? $_REQUEST['vars']['criteria_value'] : false;

            if ($criteria_name != false && $criteria_value != false) {
                switch ($criteria_name) {
                    case 'rating':
                        $where_add = ' and l.rating >= ? ';
                        break;

                    case 'city':
                        $where_add = ' and a.city = ? ';
                        break;

                    case 'reputationBadge':
                        $reputation = $this->reverse_reputation($value);
                        $where_add = ' and l.reputation <= ? ';
                        break;

                    case 'availability':
                        $where_add = ' and l.availability >= ? ';
                        break;

                    case 'category':
                        $where_add = ' and c.category = ? ';
                        break;

                    default:
                        break;
                }
            }

            // for listings sql
            $sql = $this->dbConn->prepare("SELECT l.*, a.city, a.state, a.country, a.zip, a.address,
                c.category
                FROM listings l
                inner join locations a on a.id = l.location_id
                inner join categories c on c.id = l.category_id
                where l.rating >= ? and l.rating <= ?
                and l.reputation >= ? and l.reputation <= ?
                and l.name NOT REGEXP ?
                " . $where_add . "
                order by l.id desc");

            // with search criteria
            if ($where_add != null) {
                $sql->execute(array(RATING_MIN, RATING_MAX, REPUTATION_MIN, REPUTATION_MAX, BAD_WORDS_REGEX, $criteria_value));
            } else {
                $sql->execute(array(RATING_MIN, RATING_MAX, REPUTATION_MIN, REPUTATION_MAX, BAD_WORDS_REGEX));
            }

            $count = $sql->rowCount();

            if ($count > 0) {
                while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
                    $temp = [
                        'name' => $row['name'],
                        'rating' => $row['rating'],
                        'category' => $row['category'],
                        'location' => [
                            'city' => $row['city'],
                            'state' => $row['state'],
                            'country' => $row['country'],
                            'zip_code' => $row['zip'],
                            'address' => $row['address'],
                        ],
                        'image' => $row['image'],
                        'reputation' => $row['reputation'],
                        'reputationBadge' => $this->check_reputation($row['reputation']),
                        'price' => $row['price'],
                        'availability' => $row['availability'],
                    ];

                    $data[] = $temp;

                    $result = [200, 'Listing successfully fetched', $data];
                }
            } else {
                $result = [200, 'No result found.', null];
                http_response_code(200);
            }

            return $this->set_response($result);
        } catch (\Exception $exception) {
            $this->misc->log('Search' . __METHOD__, $exception);
            http_response_code(500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/item/{id:\d+}",
     *     summary="Get Listings optional parameters",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="url",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer"
     *                 ),
     *                 example=/10
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function get_item(): string
    {
        try {
            // default
            $result = [400, 'Invalid request', null];
            $data = [];
            $id = isset($_REQUEST['vars']['id']) ? $_REQUEST['vars']['id'] : false;

            if (!$id) {
                return $this->set_response($result);
            }

            // for item sql
            $sql = $this->dbConn->prepare("SELECT l.*, a.city, a.state, a.country, a.zip, a.address,
                c.category
                FROM listings l
                inner join locations a on a.id = l.location_id
                inner join categories c on c.id = l.category_id
                where l.rating >= ? and l.rating <= ?
                and l.reputation >= ? and l.reputation <= ?
                and l.name NOT REGEXP ?
                and l.id = ?
                limit 1");
            $sql->execute(array(RATING_MIN, RATING_MAX, REPUTATION_MIN, REPUTATION_MAX, BAD_WORDS_REGEX, $id));
            $count = $sql->rowCount();

            if ($count > 0) {
                while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
                    $temp = [
                        'name' => $row['name'],
                        'rating' => $row['rating'],
                        'category' => $row['category'],
                        'location' => [
                            'city' => $row['city'],
                            'state' => $row['state'],
                            'country' => $row['country'],
                            'zip_code' => $row['zip'],
                            'address' => $row['address'],
                        ],
                        'image' => $row['image'],
                        'reputation' => $row['reputation'],
                        'reputationBadge' => $this->check_reputation($row['reputation']),
                        'price' => $row['price'],
                        'availability' => $row['availability'],
                    ];

                    $data[] = $temp;

                    $result = [200, 'Item successfully fetched', $data];
                }
            } else {
                $result = [404, 'No result found.', null];
                http_response_code(500);
            }

            return $this->set_response($result);
        } catch (\Exception $exception) {
            $this->misc->log('Search' . __METHOD__, $exception);
            http_response_code(500);
        }
    }

    /**
     * [get_category_id description]
     * @return [type] [description]
     */
    private function get_category_id($category): int
    {
        try {
            $id = 0;
            // for category
            $sql = $this->dbConn->prepare("SELECT id
                FROM categories
                where category = ?
                limit 1");
            $sql->execute(array($category));
            $count = $sql->rowCount();

            if ($count > 0) {
                while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
                    $id = intval($row['id']);
                    break;
                }
            }

            return $id;
        } catch (\Exception $exception) {
            $this->misc->log('Search' . __METHOD__, $exception);
            http_response_code(500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/item",
     *     summary="Adds a new item along with locations",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="ineger"
     *                 ),
     *                  @OA\Property(
     *                     property="category",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="city",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="state",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="country",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="zip_code",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="reputation",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="availability",
     *                     type="integer"
     *                 ),
     *                 example={
     * "name": "Super Hotel Guddyear",
     * "rating": "3",
     * "category": "hotel",
     *    "location": {
     *    "city": "Berlin",
     *    "state": "Berlin",
     *    "country": "Germany",
     *    "zip_code": "10125",
     *    "address": "Invalidenstrasse 31 , 10115, Berlin, Deutschland"
     *    },
     *    "image": "36400462.webp",
     *    "reputation": "1000",
     *    "price": "92.00",
     *    "availability": "10"
     *    }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function create(): string
    {
        try {
            $request = json_decode(file_get_contents('php://input'));

            // default
            $result = [400, 'Invalid request', null];
            $data = [];

            // validation check
			if (!empty($request) && !empty($request->name) && is_string($request->name) 
			&& !empty($request->rating) && !empty($request->category) && !empty($request->location) 
			&& !empty($request->location->city) && !empty($request->location->state) 
			&& !empty($request->location->country) && !empty($request->location->zip_code) 
			&& !empty($request->location->address) && !empty($request->image) 
			&& !empty($request->reputation) && !empty($request->price) 
			&& !empty($request->availability)) {
                // more validation
                $flag = 0;
                $arr = ["Free", "Offer", "Book", "Website"];
                if (preg_match_all('[' . implode('|', $arr) . ']', $request->name) > 0 || strlen($request->name) < 10) {
                    $flag = 1;
                }

                if (!is_numeric($request->rating) || $request->rating < 0 || $request->rating > 5) {
                    $flag = 1;
                }

                if (!is_numeric($request->reputation) || $request->reputation < 0 || $request->reputation > 1000) {
                    $flag = 1;
                }

                if (!is_numeric($request->price) || !is_numeric($request->availability)) {
                    $flag = 1;
                }

                if ($flag == 1) {
                    return $this->set_response($result);
                }

                //We start our transaction.
                $this->dbConn->beginTransaction();

                // save into locations
                $sql = $this->dbConn->prepare("INSERT into locations (city, state, country, zip, address)
                    values(?, ?, ?, ?, ?)");
				$sql->execute(array($request->location->city, $request->location->state,
				$request->location->country, $request->location->zip_code, $request->location->address));
                $location_id = $this->dbConn->lastInsertId();

                if ($location_id) {
                    // get category id
                    $category_id = $this->get_category_id($request->category);

                    if ($category_id != 0) {
                        // save into listings
                        $sql = $this->dbConn->prepare("INSERT into listings (name, rating, category_id, 
						location_id, image, reputation, price, availability)
                    values(?, ?, ?, ?, ?, ?, ?, ?)");
						$sql->execute(array($request->name, $request->rating, $category_id, $location_id,
						$request->image, $request->reputation, $request->price, $request->availability));
                        $listing_id = $this->dbConn->lastInsertId();
                    } else {
                        // roll back all transaction
                        $this->dbConn->rollBack();
                        $result = [404, 'Couldnt save', null];
                    }
                } else {
                    // roll back all transaction
                    $this->dbConn->rollBack();
                    $result = [404, 'Couldnt save', null];
                    http_response_code(404);
                }

                $data['id'] = $listing_id;
                $result = [200, 'Item successfully saved', $data];

                $this->dbConn->commit();
            }
            return $this->set_response($result);
        } catch (\Exception $exception) {
            // roll back all transaction
            $this->dbConn->rollBack();
            http_response_code(500);
            $this->misc->log('Search' . __METHOD__, $exception);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/v1/item",
     *     summary="Update a item along with locations",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="id",
     *                     type="ineger"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="rating",
     *                     type="ineger"
     *                 ),
     *                  @OA\Property(
     *                     property="category",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="city",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="state",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="country",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="zip_code",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="reputation",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="integer"
     *                 ),
     *                 @OA\Property(
     *                     property="availability",
     *                     type="integer"
     *                 ),
     *                 example={
     *              "id": "3",
     *    "name": "Super Hotel Guddyear",
     *    "rating": "3",
     *    "category": "hotel",
     *    "location": {
     *    "city": "Berlin",
     *    "state": "Berlin",
     *    "country": "Germany",
     *    "zip_code": "10125",
     *    "address": "Invalidenstrasse 31 , 10115, Berlin, Deutschland"
     *    },
     *    "image": "36400462.webp",
     *    "reputation": "1000",
     *    "price": "92.00",
     *    "availability": "10"
     *    }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function update(): string
    {
        try {
            // php can't handle PUT method directly
            $request = json_decode(file_get_contents('php://input'));

            // default
            $result = [400, 'Invalid request', null];
            $data = [];

            // validations
            if (!empty($request) && !empty($request->id) && !empty($request->name)
                && !empty($request->rating) && !empty($request->category) && !empty($request->location)
                && !empty($request->location->city) && !empty($request->location->state)
                && !empty($request->location->country) && !empty($request->location->zip_code)
                && !empty($request->location->address) && !empty($request->image)
                && !empty($request->reputation) && !empty($request->price)
                && !empty($request->availability)) {
                //We start our transaction.
                $this->dbConn->beginTransaction();

                // check that item available
                $sql = $this->dbConn->prepare("SELECT l.*, a.city, a.state, a.country, a.zip, a.address,
                c.category
                FROM listings l
                inner join locations a on a.id = l.location_id
                inner join categories c on c.id = l.category_id
                where l.rating >= ? and l.rating <= ?
                and l.reputation >= ? and l.reputation <= ?
                and l.name NOT REGEXP ?
                and l.id = ?
                limit 1");
                $sql->execute(array(RATING_MIN, RATING_MAX, REPUTATION_MIN, REPUTATION_MAX, BAD_WORDS_REGEX,
                    $request->id));
                $count = $sql->rowCount();

                if ($count > 0) {
                    $data = [];

                    while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
                        $temp = [
                            'name' => $row['name'],
                            'rating' => $row['rating'],
                            'category' => $row['category'],
                            'location' => [
                                'id' => $row['location_id'],
                                'city' => $row['city'],
                                'state' => $row['state'],
                                'country' => $row['country'],
                                'zip_code' => $row['zip'],
                                'address' => $row['address'],
                            ],
                            'image' => $row['image'],
                            'reputation' => $row['reputation'],
                            'reputationBadge' => $this->check_reputation($row['reputation']),
                            'price' => $row['price'],
                            'availability' => $row['availability'],
                        ];

                        $data = $temp;
                    }

                    // local data update if anything is different
                    if ($request->location->city != $data['location']['city'] || $request->location->state != $data['location']['state'] || $request->location->country != $data['location']['country'] || $request->location->zip_code != $data['location']['zip_code'] || $request->location->address != $data['location']['address']) {

                        if ($this->dbConn->inTransaction()) {
                            // Never gets here
                            $this->dbConn->rollback();
                        }
                        //We start our transaction.
                        $this->dbConn->beginTransaction();

                        // save into locations
						$sql = $this->dbConn->prepare("Update locations set city = ?, state = ?, country = ?, 
						zip = ?, address = ? where id = ?");
						$sql->execute(array($request->location->city, $request->location->state, $request->location->country, $request->location->zip_code, 
						$request->location->address, $data['location']['id']));

                        $this->dbConn->commit();
                    }

					if ($request->name != $data['name'] || $request->rating != $data['rating'] 
					|| $request->category != $data['category'] || $request->image != $data['image'] || $request->reputation != $data['reputation'] || $request->price != $data['price'] || $request->availability != $data['availability']) {

                        if ($this->dbConn->inTransaction()) {
                            // Never gets here
                            $this->dbConn->rollback();
                        }
                        //We start our transaction.
                        $this->dbConn->beginTransaction();

                        // get category id
                        $category_id = $this->get_category_id($request->category);
                        if ($category_id != 0) {
                            // save into listings
							$sql = $this->dbConn->prepare("update listings set name = ?, rating = ?, 
							category_id = ?, location_id = ?, image = ?, reputation = ?, price = ?, 
							availability = ? where id = ?");
							$sql->execute(array($request->name, $request->rating, $category_id, 
							$data['location']['id'], $request->image, $request->reputation, 
							$request->price, $request->availability, $request->id));

                            $result = [200, 'Item successfully updated', $request->id];

                            $this->dbConn->commit();
                        } else {
                            // roll back all transaction
                            $this->dbConn->rollBack();

                            $result = [404, 'Couldnt updated', $request->id];
                            http_response_code(404);
                        }
                    } else {
                        $result = [200, 'Nothing to updated', $request->id];
                    }
                }
            }
            return $this->set_response($result);
        } catch (\Exception $exception) {
            // roll back all transaction
            $this->dbConn->rollBack();
            http_response_code(500);
            $this->misc->log('Search' . __METHOD__, $exception);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/item",
     *     summary="Delete a item along with locations and bookings",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="id",
     *                     type="ineger"
     *                 ),
     *                 example={ "id": "3" }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function delete(): string
    {
        try {
            $request = json_decode(file_get_contents('php://input'));

            // default
            $result = [400, 'Invalid request', null];

            if ($this->dbConn->inTransaction()) {
                // Never gets here
                $this->dbConn->rollback();
            }

            // check that item available
            $sql = $this->dbConn->prepare("SELECT *
                FROM listings
                where id = ?
                limit 1");
            $sql->execute(array($request->id));
            $count = $sql->rowCount();

            if ($count > 0) {
                while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
                    $location_id = $row['location_id'];
                }

                //We start our transaction.
                $this->dbConn->beginTransaction();

                // cascade delete but we can change the status as well after add another col
                // location row will delete listing as well by cascade
                $sql = $this->dbConn->prepare("delete from locations where id = ?");
                $sql->execute(array($location_id));

                $this->dbConn->commit();

                $result = [200, 'Item successfully deleted', $request->id];
            } else {
                $result = [404, 'Nothing to delete', $request->id];
                http_response_code(404);
            }
            return $this->set_response($result);
        } catch (\Exception $exception) {
            // roll back all transaction
            $this->dbConn->rollBack();
            http_response_code(500);
            $this->misc->log('Search' . __METHOD__, $exception);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/book",
     *     summary="Book a accomodation",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                  @OA\Property(
     *                     property="id",
     *                     type="ineger"
     *                 ),
     *                 example={
     *              "id": "3" }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     )
     * )
     */
    public function book(): string
    {
        try {
            $request = json_decode(file_get_contents('php://input'));

            // default
            $result = [400, 'Invalid request', null];
            $data = [];
            $availability = 0;

            // check that item available
            $sql = $this->dbConn->prepare("SELECT *
                FROM listings
                where id = ?
                limit 1");
            $sql->execute(array($request->id));
            $count = $sql->rowCount();

            if ($count > 0) {
                while ($row = $sql->fetch(\PDO::FETCH_ASSOC)) {
                    $availability = $row['availability'];
                }

                if ($availability > 0) {
                    //We start our transaction.
                    $this->dbConn->beginTransaction();

                    // save booking
                    $sql = $this->dbConn->prepare("INSERT into bookings (listing_id)
                    values(?)");
                    $sql->execute(array($request->id));
                    $booking_id = $this->dbConn->lastInsertId();

                    if ($booking_id) {
                        // update listing with decrease value of availability
                        $sql = $this->dbConn->prepare("update listings set availability = ? where id = ?");
                        $sql->execute(array(($availability - 1), $request->id));

                        $this->dbConn->commit();

                        $result = [200, 'Item successfully booked', $request->id];

                    } else {
                        $result = [400, 'Invalid couldnt booked', $request->id];
                        // roll back all transaction
                        $this->dbConn->rollBack();
                    }

                } else {
                    $result = [200, 'Item sold out', $request->id];
                }

            } else {
                $result = [404, 'Invalid Item Id', $request->id];
                http_response_code(404);
            }

            return $this->set_response($result);

        } catch (\Exception $exception) {
            // roll back all transaction
            $this->dbConn->rollBack();
            http_response_code(500);
            $this->misc->log('Search' . __METHOD__, $exception);
        }
    }

}
