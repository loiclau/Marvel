<?php

namespace API\Entity;

abstract class Webservice
{
    public function __construct($db)
    {

    }

    abstract public function getAll();

    abstract public function get($id);

    abstract public function insert(array $data);

    abstract public function update(array $data);

    abstract public function delete($id);

}
