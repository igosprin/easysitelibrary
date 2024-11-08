<?php
namespace Easysite\Library\Route;
trait RouteClass{
    private function createUserRoutsMap(array $configRoutsMap){
        $routs = [];
        if (count($configRoutsMap) == 0)
            throw new \InvalidArgumentException('User`s routs map is empty');
        foreach ($configRoutsMap as $route => $action) {
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
                    }
                    $routs[$className]['events'][$keyParams1]['events'][$keyParams2] = $temp_action;
                }
            }
        }
        return $routs;
    }
    private function getUserStatisParametrName(string $route_parametr_name)
    {
        if (empty($route_parametr_name))
            throw new \InvalidArgumentException('None event name');

        return str_replace('&', '', $route_parametr_name);
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
            throw new \InvalidArgumentException('None event name');

        return strtolower($route_elements[0]);
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