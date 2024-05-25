<?php

/*
 * This file is part of the tp5er/think-auth
 *
 * (c) pkg6 <https://github.com/pkg6>
 *
 * (L) Licensed <https://opensource.org/license/MIT>
 *
 * (A) zhiqiang <https://www.zhiqiang.wang>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace tp5er\think\auth\keyparser;

use think\Request;
use tp5er\think\auth\contracts\KeyParser as ParserContract;
use tp5er\think\auth\contracts\KeyParserFactory;

class Factory implements KeyParserFactory
{

    /**
     * 默认解析token方式.
     */
    const defaultParsers = [
        AuthHeaders::class,
        QueryString::class,
        InputSource::class,
        Cookies::class,
        RouteParams::class
    ];
    /**
     * The chain.
     *
     * @var ParserContract []
     */
    private $parsers = [
    ];

    /**
     * The request.
     *
     * @var Request
     */
    protected $request;

    /**
     * Constructor.
     *
     * @param Request $request
     * @param ParserContract[] $parsers
     *
     * @return void
     */
    public function __construct(Request $request, array $parsers = [])
    {
        $this->request = $request;
        if (empty($chain)) {
            $this->parsers = $this->defaultParsers();
        } else {
            $this->parsers = $chain;
        }
    }

    /**
     * @return array
     */
    public function defaultParsers()
    {
        $default = [];
        foreach (self::defaultParsers as $class) {
            $default[] = new $class;
        }

        return $default;
    }

    /**
     * Get the parser chain.
     *
     * @return array
     */
    public function getParsers()
    {
        return $this->parsers;
    }

    /**
     * Set the order of the parser chain.
     *
     * @param array $parser
     *
     * @return $this
     */
    public function setParsers(array $parser)
    {
        $this->parsers = $parser;

        return $this;
    }

    /**
     * Iterate through the parsers and attempt to retrieve
     * a value, otherwise return null.
     *
     * @return string|null
     */
    public function parseToken()
    {
        foreach ($this->getParsers() as $parser) {
            if ($response = $parser->parse($this->request)) {
                return $response;
            }
        }
    }

    /**
     * Check whether a token exists in the chain.
     *
     * @return bool
     */
    public function hasToken()
    {
        return $this->parseToken() !== null;
    }

    /**
     * Set the request instance.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
