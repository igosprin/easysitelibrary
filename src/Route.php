<?php
namespace Easysite\Library;

use Easysite\Library\Request;
use Easysite\Library\Config;
use InvalidArgumentException;

class Route extends Request
{
    private array $_routs;
    
    function __construct(Config $config)
    {
        parent::__construct($config->get('languagesList',false));
        $this->_routs = $this->getUserRoutsMap($config->get('routs',[]));
        
        //$this->_languages = $languages_list;
    }
    /**
     * get element from request url
     */
    private function getUrlParam()
    {
        return explode('/', $this->getRequestUri());
    }
    /**
     * search controller and action from request url
     */
    public function searchEvent()
    {
        $action = [
            'controller' => '',
            'action' => '',
            'params' => []

        ];
        $params = $this->getUrlParam();
        if (empty($params[0])) {
            $action['controller'] = 'index';
            $action['action'] = 'index';
        } else {
            $keyRoute = strtolower($this->getRequestMethod()) . '&' . $params[0];
            if (isset($this->_routs[$keyRoute]) and isset($this->_routs[$keyRoute]['events']['&default'])) {
                $action['controller'] = $this->_routs[$keyRoute]['events']['&default']['controller'];
                $action['action'] = $this->_routs[$keyRoute]['events']['&default']['action'];
                if (isset($params[1]) and !empty($params[1])) {
                    if (isset($this->_routs[$keyRoute]['events'][$params[1]])) {
                        $temp_ = $this->_routs[$keyRoute]['events'][$params[1]];
                        if (isset($params[2]) and !empty($params[2])) {
                            if (isset($temp_['events'][$params[2]])) {
                                $action['controller'] = $temp_['events'][$params[2]]['controller'];
                                $action['action'] = $temp_['events'][$params[2]]['action'];
                                $action['a'] = 1;
                            } elseif (isset($temp_['events']['&2'])) {
                                $action['controller'] = $temp_['events']['&2']['controller'];
                                $action['action'] = $temp_['events']['&2']['action'];
                                $action['params'][$temp_['events']['&2']['param']] = $params[2];
                                $action['a'] = 2;
                            }
                        } else {
                            $action['controller'] = $temp_['controller'];
                            $action['action'] = $temp_['action'];
                        }
                    } elseif (isset($this->_routs[$keyRoute]['events']['&1'])) {
                        $temp_ = $this->_routs[$keyRoute]['events']['&1'];
                        $action['params'][$temp_['param']] = $params[1];
                        if (isset($params[2]) and !empty($params[2])) {
                            if (isset($temp_['events'][$params[2]])) {
                                $action['controller'] = $temp_['events'][$params[2]]['controller'];
                                $action['action'] = $temp_['events'][$params[2]]['action'];
                                $action['a2'] = 1;
                            } elseif (isset($temp_['events']['&2'])) {
                                $action['controller'] = $temp_['events']['&2']['controller'];
                                $action['action'] = $temp_['events']['&2']['action'];
                                $action['params'][$temp_['events']['&2']['param']] = $params[2];
                                $action['a2'] = 2;
                            }
                        } else {
                            $action['controller'] = $temp_['controller'];
                            $action['action'] = $temp_['action'];
                            $action['a2'] = 0;

                        }
                    }
                }
            }
        }
        return $action;
    }
    /**
     * get user`s routs map 
     * 
     * @param string[] $users_routs_map
     * @return array
     */
    private function getUserRoutsMap(array $users_routs_map)
    {
        $routs = [];
        if (count($users_routs_map) == 0)
            throw new InvalidArgumentException('User`s routs map is empty');
        foreach ($users_routs_map as $route => $action) {
            $elements = $this->getElementFromRoute($route);
            $method = $action['type_method'] ? strtolower($action['type_method']) : 'get';
            $className = $method . '&' . $this->getUserControllerName($elements);
            if (!isset($routs[$className]))
                $routs[$className]['events'] = ['&default' => $action];
            if (isset($elements[1])) {
                $temp_action = $action;
                $keyParams1 = $this->getUserStatisParametrName($elements[1]);
                if ($this->isParams($elements[1])) {
                    $keyParams1 = '&1';
                    $action['param'] = $this->getUserStatisParametrName($elements[1]);
                }

                if (!isset($routs[$className]['events'][$keyParams1]))
                    $routs[$className]['events'][$keyParams1] = $action;
                if (isset($elements[2])) {
                    $keyParams2 = $this->getUserStatisParametrName($elements[2]);
                    if ($this->isParams($elements[2])) {
                        $keyParams2 = '&2';
                        $temp_action['param'] = $this->getUserStatisParametrName($elements[2]);
                        //$routs[$className]['events'][$keyParams1]['events'][$keyParams2]
                    }
                    $routs[$className]['events'][$keyParams1]['events'][$keyParams2] = $temp_action;
                }
            }
        }
        //var_dump($routs);
        return $routs;

    }
    /**
     * get controller name from user`s map
     * 
     * @param array $route_elements
     * @return string
     */
    private function getUserControllerName(array $route_elements)
    {
        if (count($route_elements) == 0)
            throw new InvalidArgumentException('None event name');

        return strtolower($route_elements[0]);
    }


    /**
     * get static parametr name from user`s map
     * 
     * @param string $route_parametr_name
     * @return string
     */
    private function getUserStatisParametrName(string $route_parametr_name)
    {
        if (empty($route_parametr_name))
            throw new InvalidArgumentException('None event name');

        return str_replace('&', '', $route_parametr_name);
    }
    /**
     * get action from user`s map
     * 
     * @param string $map_action
     * @return array
     */
    private function getUserAction(array $map_action)
    {
        return [];
    }
    /**
     * get all element from user route
     * 
     * @param string $route
     * @return string[]
     */
    private function getElementFromRoute(string $route)
    {
        $elements = explode('/', $route);
        return array_values(array_filter($elements, function ($v) {
            return !empty($v);
        }));
    }

    /**
     * check for parameter
     * 
     * @param string $element
     *
     * @return boolean
     */

    private function isParams(string $element)
    {
        if (substr($element, 0, 1) === '&')
            return true;
        return false;
    }

}

