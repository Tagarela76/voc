<?php

return array(
    'eventDispatcher' => function($c) {
        return new \Symfony\Component\EventDispatcher\EventDispatcher();
    }
);