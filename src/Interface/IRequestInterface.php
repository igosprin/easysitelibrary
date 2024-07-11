<?php
namespace  Easysite\Library\Interface;
interface iRequestInterface
{
    public function getRequestUri();
    public function getRequestMethod();
    public function getRequestQuery();
    public function getRequestAccertLanguage();
    public function getRequestLanguage();
    public function getRequestRemoteAddress();
    public function getRequestIpAddress();
}