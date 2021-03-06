<?php namespace Drivers\Models;

/**
 *This is the Base Model class that all Model Classes extend. All methods in this class
 *are implemented in a static mannger so no instance of this can be created.
 *
 *@author Geoffrey Oliver <geoffrey.oliver2@gmail.com>
 *@copyright 2015 - 2020 Geoffrey Oliver
 *@category Core
 *@package Core\Drivers\Models
 *@link https://github.com/gliver-mvc/gliver
 *@license http://opensource.org/licenses/MIT MIT License
 *@version 1.0.1
 */

use Drivers\Registry;

class BaseModelClass {

	/**
	 *This is the constructor class. We make this private to avoid creating instances of
	 *this Register object
	 *
	 *@param null
	 *@return void
	 */
	private function __construct() {}

	/**
	 *This method stops creation of a copy of this object by making it private
	 *
	 *@param null
	 *@return void
	 *
	 */
	private function __clone(){}

	/**
	 *@var object Resource Instance of the database object
	 */
	protected static $connection;

	/**
	 *@var object Instance of the database query object
	 */
	protected static $queryObject;

	/**
	*@var bool Sets whether the query table has been set
	*/
	protected static $queryTableSet = false;

	/**
	*@var bool Stores whether database connection made
	*/
	protected static $dbConnectionMade = false;

	/**
	 *This method returns a query instance
	 *
	 *@param null
	 *@return object Query instance
	 */
	protected static function Query()
	{
		//get the connection resource, if not set yet
        if(static::$connection === null) static::$connection = Registry::get('database');

        //set the query builder instance, if not set
        if(static::$queryObject === null) static::$queryObject = static::$connection->query();

        //set the value of $dbConnectionMade to true, if false
        if(static::$dbConnectionMade === false) static::$dbConnectionMade = true;

        //return the query object
        return static::$queryObject;

	}

	/**
	 *This method sets the table name and fields upon which to perform database queries.
	 *@param string $from The table name upon which to perform query
	 *@param array $fields The names of the fields to select in numeric array
	 *@return object $this
	 */
	final public static function from($from = null, $fields = array("*"))
	{
		//call the from method of this query instance to set table name and fields if table name provided
		if(static::$queryTableSet === false AND $from !== null) {

			//call method to set table and field names
			static::Query()->from($from, $fields);

			//set the $queryTableSet = true
			static::$queryTableSet = true;

	        //return the static class
	        return new static;

		} 

		//table name not provided, get the table name from class property
		elseif(static::$queryTableSet === false AND $from === null){

			//call method to set table and field names
			static::Query()->from(static::$table, $fields);

			//set the $queryTableSet = true
			static::$queryTableSet = true;
			
	        //return the static class
	        return new static;

		}

		//the table name and fields are already set, return query object
		else{

	        //return the static class
	        return new static;

		}

	}

	/**
	 *This method builds query string for joining tables in query.
	 *@param string $join The type of join to performa
	 *@param string $table the table to perform join on
	 *@param string $on The conditions for the join
	 *@param array $fields The fields name to join in numeric array
	 *@return object $this
	 */
	final public static function join($join, $table, $on, $fields = array() )
	{
		//call the join method of the query object
		static::Query()->join($join, $table, $on, $fields);

		//return static class
		return new static;

	}

	/**
	 *This method sets the limit for the number of rows to return.
	 *@param int $limit The maximum number of rows to return per query
	 *@param int $page An interger used to define the offset of the select query
	 *@return object $this
	 */
	final public static function limit($limit, $page = 1)
	{
		//call the limit method of the query builder object
		static::Query()->limit($limit, $page);

		//return static class
		return new static;

	}
	
	/**
	 *This method sets the DISTINCT param in query string to only return non duplicated values in a column.
	 *@param null
	 *@return Object $this
	 */
	final public static function unique()
	{
		//call the unique method of the query builder
		static::Query()->unique();

		//return static class
		return new static;

	}

	/**
	 *This method sets the order in which to sort the query results.
	 *@param string $order The name of the field to use for sorting
	 *@param string $direction This specifies whether sorting should be in ascending or descending order
	 *@return Object $this
	 */
	final public static function order($order, $direction = 'asc')
	{
		//call the order method of the query builder
		static::Query()->order($order, $direction);

		//return static class
		return new static;

	}

	/**
	 *This method defines the where parameters of the query string.
	 *@param mixed Thie method expects an undefined number of arguments
	 *@return Object static class
	 */
	final public static function where()
	{
		//call the query builder object where method passing the argument list
		static::Query()->where(func_get_args());

		//return static class
		return new static;

	}

	/**
	 *This methods inserts/updates one row of data into the database.
	 *@param array The array containing the data to be inserted
	 *@return \MySQLResponseObject
	 */
	final public static function save($data)
	{
		//call the from method and return response object
		static::from();
		return static::$queryObject->save($data, static::$update_timestamps);

	}
	
	/**
	 *The method perform insert/update of large amounts of data.
	 *@param array The data to be inserted/updated in a multidimensional array
	 *@param array The fields into which the data is to be inserted ot updated
	 *@param array For update query, The unique id fields to use for updating
	 *@return \MySQLResponseObject
	 */
	final public static function saveBulk($data, $fields = null, $ids = null, $key = null)
	{
		//call the from method and return response object
		static::from();
		return static::$queryObject->saveBulk($data, $fields, $ids, $key, static::$update_timestamps);

	}

	/**
	 *This method deletes a set of rows that match the query parameters provided.
	 *@param null
	 *@return \MySQLResponseObject
	 */ 
	final public static function delete()
	{
		//call the from method and return response object
		static::from();
		return static::$queryObject->delete();

	}

	/**
	 *This method returns the first row match in a query.
	 *@param null
	 *@return \MySQLResponseObject
	 */
	final public static function first()
	{
		//call the from method and return response object
		static::from();
		return static::$queryObject->first();

	}

	/**
	 *This method counts the number of rows returned by query.
	 *@param null
	 *@return \MySQLResponseObject
	 */
	final public static function count()
	{
		//call the from method and return response object
		static::from();
		return static::$queryObject->count();

	}

	/**
	 *This method returns all rows that match the query parameters.
	 *@param null
	 *@return \MySQLResponseObject
	 */
	final public static function all()
	{
		//call the from method and return response object
		static::from();
		return static::$queryObject->all();

	}

	/**
	 *This method executes a raw query in the database.
	 *@param string $query_string The query string to execute
	 *@return \MySQLResponseObject
	 *@throws \MySQLException if query returned an error message string
	 */
	final public static function rawQuery($query_string)
	{
		//call the raw method and return response object
		return static::Query()->rawQuery($query_string);

	}

}