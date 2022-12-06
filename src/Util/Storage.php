<?php

namespace Supabase\Util;

class Storage {

    protected $values = array();

    public function __get( $key ) {
        return $this->values[ $key ];
    }

    public function __set( $key, $value ) {
        $this->values[ $key ] = $value;
    }

    public function dump() {
        return var_dump( $this->values );
    }

}