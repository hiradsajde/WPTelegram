<?php

namespace wpbot\manager;

class json
{
    public function decode($url)
    {
        return json_decode(file_get_contents($url));
    }
}
