<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This file is part of SphinxQL for Kohana.
 *
 * Copyright (c) 2010, Deoxxa Development
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package kohana-sphinxql
 */

/**
 * Class for building queries to send to sphinx
 *
 * @package kohana-sphinxql
 * @author MasterCJ <mastercj@mastercj.net>
 * @version 0.1
 * @license http://mastercj.net/license.txt
 */
class Kohana_SphinxQL_Query {
    /**
     * @var array The indexes that are to be searched
     */
    protected $_indexes = array();
    /**
     * @var array The fields that are to be returned in the result set
     */
    protected $_fields = array();
    /**
     * @var string A string to be searched for in the indexes
     */
    protected $_search = null;
    /**
     * @var array A set of WHERE conditions
     */
    protected $_wheres = array();
    /**
     * @var array The GROUP BY field
     */
    protected $_group = null;
    /**
     * @var array The IN GROUP ORDER BY options
     */
    protected $_group_order = null;
    /**
     * @var array A set of ORDER clauses
     */
    protected $_orders = array();
    /**
     * @var integer The offset to start returning results from
     */
    protected $_offset = 0;
    /**
     * @var integer The maximum number of results to return
     */
    protected $_limit = 20;
    /**
     * @var array A set of OPTION clauses
     */
    protected $_options = array();
    /**
     * @var SphinxQL_Core A reference to a SphinxQL_Core object, used for the execute() function
     */
    protected $_sphinx = null;

    /**
     * Constructor
     *
     * @param SphinxQL_Core $sphinx
     */
    public function __construct(SphinxQL_Core $sphinx) {
        $this->sphinx($sphinx);
    }

    /**
     * Magic method, returns the result of build().
     *
     * @return string
     */
    public function __toString() {
        return $this->build();
    }

    /**
     * Sets or gets the SphinxQL_Core object associated with this query.
     * If you pass it nothing, it'll return $this->_sphinx
     * If you pass it a SphinxQL_Core object, it'll return $this
     * If you pass it anything else, it'll return false
     *
     * @return SphinxQL_Query|SphinxQL_Core|false $this or $this->_sphinx or error
     */
    public function sphinx($sphinx=null) {
        if (is_a($sphinx, 'SphinxQL_Core')) {
            $this->_sphinx = $sphinx;
            return $this;
        } elseif($sphinx === null) {
            return $sphinx;
        }

        return false;
    }

    /**
     * Builds the query string from the information you've given.
     *
     * @return string The resulting query
     */
    public function build() {
        $fields = array();
        $wheres = array();
        $orders = array();
        $options = array();
        $query = '';

        foreach ($this->_fields as $field) {
            if (!isset($field['field']) OR !is_string($field['field'])) { next; }
            if (isset($field['alias']) AND is_string($field['alias'])) {
                $fields[] = sprintf("%s AS %s", $field['field'], $field['alias']);
            } else {
                $fields[] = sprintf("%s", $field['field']);
            }
        } unset($field);

        if (is_string($this->_search)) {
            $wheres[] = sprintf("MATCH('%s')", addslashes($this->_search));
        }

        foreach ($this->_wheres as $where) {
            $wheres[] = sprintf("%s %s %s", $where['field'], $where['operator'], $where['value']);
        } unset($where);

        foreach ($this->_orders as $order) {
            $orders[] = sprintf("%s %s", $order['field'], $order['sort']);
        } unset($order);

        foreach ($this->_options as $option) {
            if ('max_matches' == $option['name']) {
                $max_matches = $option['value'];
                continue;
            }
            $options[] = sprintf("%s=%s", $option['name'], $option['value']);
        } unset($option);

        $query .= sprintf('SELECT %s ', count($fields) ? implode(', ', $fields) : '*');
        $query .= sprintf('FROM %s ', implode(',', $this->_indexes));
        if (count($wheres) > 0) { $query .= sprintf('WHERE %s ', implode(' AND ', $wheres)); }
        if (is_string($this->_group)) { $query .= sprintf('GROUP BY %s ', $this->_group); }
        if (is_array($this->_group_order)) { $query .= sprintf('WITHIN GROUP ORDER BY %s %s ', $this->_group_order['field'], $this->_group_order['sort']); }
        if (count($orders) > 0) { $query .= sprintf('ORDER BY %s ', implode(', ', $orders)); }
        $query .= sprintf('LIMIT %d, %d ', $this->_offset, $this->_limit);
        if (count($options) > 0) { $query .= sprintf('OPTION %s ', implode(', ', $options)); }
        if ( ! isset($max_matches)) {
            $max_matches = (string)max(1000, $this->_offset + $this->_limit + 20);
        }
        $query .= sprintf('OPTION max_matches=%s', $max_matches);
        while (substr($query, -1, 1) == ' ') { $query = substr($query, 0, -1); }

        return $query;
    }

    /**
     * Adds an entry to the list of indexes to be searched.
     *
     * @param string The index to add
     * @return SphinxQL_Query $this
     */
    public function add_index($index) {
        if (is_string($index)) {
            array_push($this->_indexes, $index);
        }

        return $this;
    }

    /**
     * Removes an entry from the list of indexes to be searched.
     *
     * @param string The index to remove
     * @return SphinxQL_Query $this
     */
    public function remove_index($index) {
        if (is_string($index)) { 
            while ($pos = array_search($index, $this->_indexes)) {
                unset($this->_indexes[$pos]);
            }
        }

        return $this;
    }

    /**
     * Adds a entry to the list of fields to return from the query.
     *
     * @param string Field to add
     * @param string Alias for that field, optional
     * @return SphinxQL_Query $this
     */
    public function add_field($field, $alias=null) {
        if (!is_string($alias)) {
            $alias = null;
        }

        if (is_string($field)) {
            $this->_fields[] = array('field' => $field, 'alias' => $alias);
        }

        return $this;
    }

    /**
     * Adds multiple entries at once to the list of fields to return.
     * Takes an array structured as so:
     * array(array('field' => 'user_id', 'alias' => 'user')), ...)
     * The alias is optional.
     *
     * @param array Array of fields to add
     * @return SphinxQL_Query $this
     */
    public function add_fields($array) {
        if (is_array($array)) {
            foreach ($array as $entry) {
                if (is_array($entry) AND isset($entry['field'])) {
                    if (!isset($entry['alias']) OR is_string($entry['alias'])) {
                        $entry['alias'] = null;
                        $this->add_field($entry['field'], $entry['alias']);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Removes a field from the list of fields to search.
     *
     * @param string Alias of the field to remove
     * @return SphinxQL_Query $this
     */
    public function remove_field($alias) {
        if (is_string($alias) AND array_key_exists($this->_fields, $alias)) {
            unset($this->_fields[$alias]);
        }

        return $this;
    }

    /**
     * Removes multiple fields at once from the list of fields to search.
     *
     * @param array List of aliases of fields to remove
     * @return SphinxQL_Query $this
     */
    public function remove_fields($array) {
        if (is_array($array)) {
            foreach ($array as $alias) {
                $this->remove_field($alias);
            }
        }

        return $this;
    }

    /**
     * Sets the text to be matched against the index(es)
     *
     * @param string Text to be searched
     * @return SphinxQL_Query $this
     */
    public function search($search) {
        if (is_string($search)) {
            $this->_search = $search;
        }

        return $this;
    }

    /**
     * Removes the search text from the query.
     *
     * @return SphinxQL_Query $this
     */
    public function remove_search() {
        $this->_search = null;

        return $this;
    }

    public function getSearch(){
        return $this->_search;
    }

    /**
     * Sets the offset for the query
     *
     * @param integer Offset
     * @return SphinxQL_Query $this
     */
    public function offset($offset) {
        if (is_integer($offset)) {
            $this->_offset = $offset;
        }

        return $this;
    }

    /**
     * Sets the limit for the query
     *
     * @param integer Limit
     * @return SphinxQL_Query $this
     */
    public function limit($limit) {
        if (is_integer($limit)) {
            $this->_limit = $limit;
        }

        return $this;
    }

    /**
     * Adds a WHERE condition to the query.
     *
     * @param string The field/expression for the condition
     * @param string The field/expression/value to compare the field to
     * @param string The operator (=, <, >, etc)
     * @param bool DEPRECATED
     * @return SphinxQL_Query $this
     */
    public function where($field, $value, $operator='=', $quote=null) {
        $allowed_operators = array('=', '!=', '>', '<', '>=', '<=', 'AND', 'NOT IN', 'IN', 'BETWEEN');
        if (!in_array($operator, $allowed_operators)) {
            throw new InvalidArgumentException('Argument $operator must be one of "' . implode('", "', $allowed_operators) . '", got "' . $operator . '".');
        }
        if (!is_string($field)) {
            throw new InvalidArgumentException('Argument $field must be string, got ' . gettype($field) . '.');
        }
        $value = (string)$value;
        if (null !== $quote) {
            $e = new Exception;
            Kohana::$log->add(Log::NOTICE, 'Argument $quote is deprecated, do not use it. :trace', [
                ':trace' => $e->getTraceAsString(),
            ]);
        }

        $this->_wheres[] = array('field' => $field, 'operator' => $operator, 'value' => $value);

        return $this;
    }

    /**
     * Remove from where by key field
     *
     * @param string $field The key of delete from where
     * @return $this
     */
    public function remove_where($field) {
        foreach($this->_wheres as $k => $w){
            if($w['field'] == $field){
                unset($this->_wheres[$k]);
            }
        }
        return $this;
    }

    /**
     * Adds a WHERE <field> <not> IN (<value x>, <value y>, <value ...>) condition to the query, mainly used for MVAs.
     *
     * @param string The field/expression for the condition
     * @param array The values to compare the field to
     * @param string Whether this is a match-all, match-any (default) or match-none condition
     * @return SphinxQL_Query $this
     */
    public function where_in($field, $values, $how='any') {
        if (!is_array($values)) {
            $values = array($values);
        }

        if ($how == 'all') {
            foreach ($values as $value) {
                $this->where($field, $value, '=');
            }
        } elseif ($how == 'none') {
            foreach ($values as $value) {
                $this->where($field, $value, '!=');
            }
        } else {
            $this->where($field, '('.implode(', ', $values).')', 'IN');
        }

        return $this;
    }

    /**
     * Sets the GROUP BY condition for the query.
     *
     * @param string The field/expression for the condition
     * @return SphinxQL_Query $this
     */
    public function group_by($field) {
        if (is_string($field)) {
            $this->_group = $field;
        }

        return $this;
    }

    /**
     * Removes the GROUP BY condition from the query.
     *
     * @param string The field/expression for the condition
     * @param string The alias for the result set (optional)
     * @return SphinxQL_Query $this
     */
    public function remove_group_by($field) {
        $this->_group = null;

        return $this;
    }

    /**
     * Adds an ORDER condition to the query.
     *
     * @param string The field/expression for the condition
     * @param string The sort type (can be 'asc' or 'desc', capitals are also OK)
     * @return SphinxQL_Query $this
     */
    public function order($field, $sort) {
        if (is_string($field) AND is_string($sort)) {
            $this->_orders[] = array('field' => $field, 'sort' => $sort);
        }

        return $this;
    }

    /**
     * Sets the WITHIN GROUP ORDER BY condition for the query. This is a
     * Sphinx-specific extension to SQL.
     *
     * @param string The field/expression for the condition
     * @param string The sort type (can be 'asc' or 'desc', capitals are also OK)
     * @return SphinxQL_Query $this
     */
    public function group_order($field, $sort) {
        if (is_string($field) AND is_string($sort)) {
            $this->_group_order = array('field' => $field, 'sort' => $sort);
        }

        return $this;
    }

    /**
     * Removes the WITHIN GROUP ORDER BY condition for the query. This is a
     * Sphinx-specific extension to SQL.
     *
     * @return SphinxQL_Query $this
     */
    public function remove_group_order() {
        $this->_group_order = null;

        return $this;
    }

    /**
     * Adds an OPTION to the query. This is a Sphinx-specific extension to SQL.
     *
     * @param string The option name
     * @param string The option value
     * @return SphinxQL_Query $this
     */
    public function option($name, $value) {
        if (is_string($name) AND is_string($value)) {
            $this->_options[] = array('name' => $name, 'value' => $value);
        }

        return $this;
    }

    /**
     * Removes an OPTION from the query.
     *
     * @param string The option name
     * @param string The option value, optional
     * @return SphinxQL_Query $this
     */
    public function remove_option($name, $value=null) {
        $changed = false;

        if (is_string($name) AND (($value == null) OR is_string($value))) {
            foreach ($this->_options as $key => $option) {
                if (($option['name'] == $name) AND (($value == null) OR ($value == $option['value']))) {
                    unset($this->_options[$key]);
                    $changed = true;
                }
            }

            if ($changed) {
                array_keys($this->_options);
            }
        }

        return $this;
    }

    /**
     * Executes the query and returns the results
     *
     * @return array Results of the query
     */
    public function execute() {
        return $this->_sphinx->query($this);
    }
    
    /**
     * Executes the query and returns the results or throws exception on error
     *
     * @return array Results of the query
     * @throws ErrorException
     */
    public function executeOrFail()
    {
        $result = $this->execute();
        if ( false === $result['data'] || isset($result['total_info']['error']) )
        {
            throw new Database_Exception(':error [ :query ]', array(
                    ':error' => Arr::path($result, 'total_info.error', 'Unknown error while executing query on sphinx search'),
                    ':query' => Arr::get($result, 'query'),
                ));
        }
        
        return $result;
    }
    
}
