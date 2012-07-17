<?php

namespace VWM\Framework;

abstract class Model
{

    protected function beforeSave() {
        return true;
    }

    public function save() {
        $this->beforeSave();
    }
}
