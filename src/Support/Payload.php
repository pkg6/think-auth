<?php

namespace tp5er\think\auth\Support;

use think\helper\Arr;


class Payload
{
    /**
     * @var array
     */
    protected $payload;

    /**
     * Payload constructor.
     * @param $payload
     */
    public function __construct($payload)
    {
        $type = gettype($payload);
        if ($type === 'object') {
            $this->payload = json_decode(json_encode($payload), true);
        } elseif ($type === 'array' && !empty($payload)) {
            $this->payload = $payload;
        } else {
            $this->payload = [];
        }
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->payload, $key, $default);
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->payload;
    }
}