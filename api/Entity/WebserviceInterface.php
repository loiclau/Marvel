<?php

namespace API\Entity;

interface WebserviceInterface
{

    public function getAll();

    public function get($id);

    public function insert(array $data);

    public function update(array $data);

    public function delete($id);

}
