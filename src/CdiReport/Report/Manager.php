<?php

namespace CdiReport\Report;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Manager
 *
 * @author cincarnato
 */
class Manager {

    protected $xAxis;
    protected $yAxis;
    protected $xAxisType;
    protected $yAxisType;

    function __construct() {
        $this->xAxis = array();
        $this->yAxis = array();
    }

    public function xAxisAddElement($element, $index, $value) {
        if(key_exists($index, $this->yAxis)){
        $this->xAxis[$index][$element] = $value;
        }else{
            throw new Exception("The Key no exist", "10");
        }
    }

    public function yAxisAddElement($index, $value) {
        $this->yAxis[$index] = $value;
    }
    
    public function processLoadYaxisByDate(\DateTime $startDate, \DateTime $endDate){

    }

    public function processObjectCollectionSet($element, $collection, $index, $value) {
        $methodIndex = "get" . ucfirst($index);
        $methodValue = "get" . ucfirst($value);
        $count = array();
        foreach ($collection as $objetc) {
            if (method_exists($objetc, $methodIndex) && method_exists($objetc, $methodValue)) {
                $y = $objetc->$methodIndex();
                $x = $objetc->$methodValue();
                $count[$y] = $x;
            }
        }

        foreach ($count as $key => $val) {
            $this->xAxisAddElement($element, $key, $val);
        }
    }

    public function processObjectCollectionCount($element, $collection, $index) {
        $methodIndex = "get" . ucfirst($index);
        $count = array();
        foreach ($collection as $objetc) {
            if (method_exists($objetc, $methodIndex)) {
                $y = $objetc->$methodIndex();
                $count[$y]++;
            }
        }

        foreach ($count as $key => $val) {
            $this->xAxisAddElement($element, $key, $val);
        }
    }

    public function processObjectCollectionCountByCriteria($element, $collection, $index, $value, $comparedOperator = "==", $comparedVariable = null) {
        $methodIndex = "get" . ucfirst($index);
        $methodValue = "get" . ucfirst($value);
        $count = array();
        foreach ($collection as $objetc) {

            if (method_exists($objetc, $methodIndex) && method_exists($objetc, $methodValue)) {
                $y = $objetc->$methodIndex();
                $comparedValue = $objetc->$methodValue();


                switch ($comparedOperator) {
                    case "==":
                        if ($comparedValue == $comparedVariable) {
                            $count[$y]++;
                        }

                        break;
                    case "!=":
                        if ($comparedValue != $comparedVariable) {
                            $count[$y]++;
                        }

                        break;
                    case ">":
                        if ($comparedValue > $comparedVariable) {
                            $count[$y]++;
                        }

                        break;
                    case "<":
                        if ($comparedValue < $comparedVariable) {
                            $count[$y]++;
                        }

                        break;
                    default:
                        break;
                }
            }
        }

        foreach ($count as $key => $value) {
            $this->xAxisAddElement($element, $key, $val);
        }
    }

}

?>
