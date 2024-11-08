<?php
namespace Easysite\Library;

use Easysite\Library\Request;
use Easysite\Library\Config;
use Easysite\Library\Instance\Cache;
use Easysite\Library\Route\RouteClass;

class Route extends Request
{
    use RouteClass;
    private array $_routs;
    
    function __construct()
    {
        parent::__construct(Config::get('languagesList',false));
        Log::log('');
        $this->_routs = $this->getUserRoutsMap(Config::get('routs',[]));
        
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
        $cacheKey='system:user_route_map';
        return Cache::make($cacheKey,function() use ($users_routs_map){
            return $this->createUserRoutsMap($users_routs_map);
        });
    }
    

}

