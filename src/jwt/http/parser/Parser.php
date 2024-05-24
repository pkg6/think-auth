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

namespace tp5er\think\auth\jwt\http\parser;

use think\Request;
use tp5er\think\auth\jwt\contracts\Parser as ParserContract;

class Parser
{
    /**
     * The chain.
     *
     * @var ParserContract []
     */
    private $chain = [
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
     * @param ParserContract[] $chain
     *
     * @return void
     */
    public function __construct(Request $request, array $chain = [])
    {
        $this->request = $request;
        if (empty($chain)) {
            $this->chain = $this->defaultChain();
        } else {
            $this->chain = $chain;
        }
    }

    public function defaultChain()
    {
        return [
            new  AuthHeaders,
            new  QueryString,
            new  InputSource,
            new  Cookies,
            new  RouteParams
        ];
    }

    /**
     * Get the parser chain.
     *
     * @return array
     */
    public function getChain()
    {
        return $this->chain;
    }

    /**
     * Set the order of the parser chain.
     *
     * @param array $chain
     *
     * @return $this
     */
    public function setChain(array $chain)
    {
        $this->chain = $chain;

        return $this;
    }

    /**
     * Alias for setting the order of the chain.
     *
     * @param array $chain
     *
     * @return $this
     */
    public function setChainOrder(array $chain)
    {
        return $this->setChain($chain);
    }

    /**
     * Iterate through the parsers and attempt to retrieve
     * a value, otherwise return null.
     *
     * @return string|null
     */
    public function parseToken()
    {
        foreach ($this->chain as $parser) {
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
