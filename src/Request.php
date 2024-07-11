<?php
namespace Easysite\Library;

use Easysite\Library\Interface\iRequestInterface;

class Request implements iRequestInterface
{
    private string $_request_uri;
    private string $_request_method;
    private string $_request_query_string;
    private string $_request_accept_language;
    private string $_request_language;
    private string $_request_IpAddress;
    private string $_request_remote_address;

    function __construct(array $languages_list = ['eng'])
    {
        $requestParam = $this->getRequestUrl($languages_list);
        $this->_request_uri = $requestParam['uri'];
        $this->_request_method = $_SERVER['REQUEST_METHOD'];
        $this->_request_query_string = $_SERVER['QUERY_STRING'];
        $this->_request_accept_language = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $this->_request_language = $requestParam['languages'];
        $this->_request_IpAddress = $_SERVER['REMOTE_ADDR'];
        $this->_request_remote_address = $_SERVER['REMOTE_HOST'] ?? '';
    }

    private function getRequestUrl(array $languages_list)
    {
        $uri = explode('?', str_replace(['"', "'"], '', urldecode($_SERVER['REQUEST_URI'])));

        $tmp_ = explode('/', $uri[0]);
        unset($tmp_[0]);

        $request_data = [
            'languages' => $languages_list[0],
            'uri' => join('/', $tmp_)
        ];
        if (count($tmp_) > 0) {

            if (array_search($tmp_[1], $languages_list)) {
                $request_data['languages'] = $tmp_[1];
                unset($tmp_[1]);
                $request_data['uri'] = join('/', $tmp_);
            }
        }
        return $request_data;

    }
    /**
     * get request url
     * 
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->_request_uri;
    }
    /**
     * get request method
     * 
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->_request_method;
    }
    /**
     * get request query
     * 
     * @return string
     */
    public function getRequestQuery(): string
    {
        return $this->_request_query_string;
    }
    /**
     * get request accert language
     * 
     * @return string
     */
    public function getRequestAccertLanguage(): string
    {
        return $this->_request_accept_language;
    }
    /**
     * get current language
     * 
     * @return string
     */
    public function getRequestLanguage(): string
    {
        return $this->_request_language;
    }
    /**
     * get remote ip address
     * 
     * @return string
     */
    public function getRequestIpAddress(): string
    {
        return $this->_request_IpAddress;
    }
    /**
     * get remote address
     * 
     * @return string
     */
    public function getRequestRemoteAddress(): string
    {
        return $this->_request_remote_address;
    }
}