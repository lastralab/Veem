<?php
/**
 * Created by PhpStorm.
 * User: lazaro
 * Date: 7/01/19
 * Time: 11:13 AM
 */

namespace Veem\Payment\Api\Data;


interface UrlBuilderInterface
{
    const SANDBOX_URI = 'https://sandbox-api.veem.com/';

    const LIVE_URI = 'https://api.veem.com/';


    /**
     * @return string
     */
    public function build();

    /**
     * @return string
     */
    public function getUri();

    /**
     * @param string $uri
     * @return $this
     */
    public function setUri($uri);
}