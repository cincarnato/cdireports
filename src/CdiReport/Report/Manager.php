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

    public function xAxisAddElement($rangeType, $index, $element, $value) {
        if (key_exists($index, $this->yAxis)) {
            $this->xAxis[$rangeType][$index][$element] = $value;
        } else {
            throw new Exception("The Key no exist", "10");
        }
    }

    public function yAxisAddElement($rangeType, $index, $value) {
        $this->yAxis[$rangeType][$index] = $value;
    }

    public function processLoadYaxisByInteger($startInt, $endInt, $range = 1) {
        if ($startInt < $endInt) {
            while ($startInt < $endInt) {
                $this->yAxisAddElement("integer", $startInt, $startInt);
                $startInt = $startInt + $range;
            }
        }
    }

    public function processLoadYaxisByDate(\DateTime $startDate, \DateTime $endDate, $format = "Y-m-d", $rangeType = "day") {

        if ($startDate < $endDate) {
            while ($startDate < $endDate) {

                $this->yAxisAddElement($rangeType, $startDate->format($format), $startDate->format($format));


                switch ($rangeType) {
                    case "hour":
                        $startDate->modify("+1 hour");
                        break;
                    case "day":
                        $startDate->modify("+1 day");
                        break;
                    case "week":
                        $startDate->modify("+1 week");
                        break;
                    case "month":
                        $startDate->modify("+1 month");
                        break;

                    default:
                        $startDate->modify("+1 day");
                        break;
                }
            }
        }
    }

    public function processObjectCollectionSet($element, $collection, $index, $value, $rangeType = 'day') {
        $methodIndex = "get" . ucfirst($index);
        $methodValue = "get" . ucfirst($value);
        $count = array();
        foreach ($collection as $object) {
            if (method_exists($object, $methodIndex) && method_exists($object, $methodValue)) {

                $y = $this->processReturnY($object->$methodIndex(), $rangeType);


                $x = $object->$methodValue();
                $count[$y] = $x;
            }
        }

        foreach ($count as $key => $val) {
            $this->xAxisAddElement($rangeType, $key, $element, $val);
        }
    }

    public function processObjectCollectionCount($element, $collection, $index, $rangeType = 'day') {
        $methodIndex = "get" . ucfirst($index);
        $count = array();
        foreach ($collection as $object) {
            if (method_exists($object, $methodIndex)) {

                $y = $this->processReturnY($object->$methodIndex(), $rangeType);


                $count[$y]++;
            }
        }

        foreach ($count as $key => $val) {
            $this->xAxisAddElement($rangeType, $key, $element, $val);
        }
    }

    public function processObjectCollectionCountByCriteria($element, $collection, $index, $value, $comparedOperator = "==", $comparedVariable = null, $rangeType = 'day') {
        $methodIndex = "get" . ucfirst($index);
        $methodValue = "get" . ucfirst($value);
        $count = array();
        foreach ($collection as $object) {

            if (method_exists($object, $methodIndex) && method_exists($object, $methodValue)) {
                $y = $this->processReturnY($object->$methodIndex(), $rangeType);

                $comparedValue = $object->$methodValue();


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
            $this->xAxisAddElement($rangeType, $key, $element, $val);
        }
    }

    public function processReturnY($valueY, $rangeType) {
        switch ($rangeType) {
            case "integer":
                $y = $valueY;
                if (!is_a($y, "integer")) {
                    throw new Exception("Index must be Integer");
                }

                break;
            case "string":
                $y = $valueY;
                if (!is_a($y, "string")) {
                    throw new Exception("Index must be String");
                }

                break;
            case "day":
                $yDt = $valueY;
                if (is_a($y, "DateTime")) {
                    $y = $yDt->format("Y-m-d");
                } else {
                    throw new Exception("Index must be Datetime");
                }

                break;
            case "month":
                $yDt = $valueY;
                if (is_a($y, "DateTime")) {
                    $y = $yDt->format("Y-m");
                } else {
                    throw new Exception("Index must be Datetime");
                }

                break;

            default:
                throw new Exception("rangeType must be defined");
                break;
        }

        return $y;
    }

}

?>
