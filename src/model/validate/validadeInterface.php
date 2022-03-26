<?php

namespace data\validate;

interface validadeInterface
{
    public function validate($value);

    public function getError();

    public function setError($error);
}