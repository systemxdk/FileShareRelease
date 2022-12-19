<?php

/**
 * Shaper filter class
 */
class shFilter {

    /**
     *
     * @var array
     */
    static private $filters         = array();

    /**
     *
     * @var array
     */
    static private $ignoreFilters   = array();

    /**
     * Add filters to runthrough
     *
     * @param <type> $filter
     */
    static public function AddFilter($filter){
        array_push(self::$filters, $filter);
    }

    /**
     * Adds name of filter to ignore
     *
     * @param string $filter
     */
    static public function IgnoreFilter($filter){
        array_push(self::$ignoreFilters, $filter);
    }

    /**
     * Returns filters in array
     *
     * @return array
     */
    static public function GetFilters(){
        return self::$filters;
    }

    /**
     * Fetches array of filternames to be ignored
     *
     * @return array
     */
    static public function GetIgnoreFilters(){
        return self::$ignoreFilters;
    }
}