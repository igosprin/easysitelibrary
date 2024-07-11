<?php
namespace Easysite\Library;

use Easysite\Library\Request;
use Easysite\Library\View\View;

class Controller
{
    public $view;
    public $_url_params;
    public $_request;

    function __construct()
    {
        $this->view = new View;
    }
    private function setRequest(array $languages_list = ['eng'])
    {
        $this->_request = new Request($languages_list);
    }
    public function getRequest()
    {
        return $this->_request;
    }

    private function setViewPath(string $path)
    {
        $this->view->setViewPath($path);
    }



}