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
    protected $xAxisSum;
    protected $yAxis;
    protected $xAxisType;
    protected $yAxisType;
    protected $timeZone = null;

    function __construct() {
        $this->xAxis = array();
        $this->yAxis = array();
        $this->xAxisSum = array();
    }

    public function getTimeZone() {
        return $this->timeZone;
    }

    public function setTimeZone($timeZone) {
        $this->timeZone = $timeZone;
    }

    public function getXAxis() {
        return $this->xAxis;
    }

    public function getYAxis() {
        return $this->yAxis;
    }

    public function orderYaxis($rangeType, $order) {

        if ($order == "ASC") {
            krsort($this->yAxis[$rangeType]);
        }

        if ($order == "DESC") {
            ksort($this->yAxis[$rangeType]);
        }
    }

    public function xAxisAddElement($rangeType, $index, $element, $value) {
        if (key_exists($index, $this->yAxis[$rangeType])) {
            $this->xAxis[$element][$rangeType][$index] = $value;
        } else {
            echo $index;
            throw new \Exception("The Key no exist", "10");
        }
    }

    public function yAxisAddElement($rangeType, $index, $value) {
        //echo "<br>" . $index;
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

            $this->yAxisAddElement($rangeType, $startDate->format($format), $startDate->format($format));


            while ($startDate->format($format) < $endDate->format($format)) {


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
                $this->yAxisAddElement($rangeType, $startDate->format($format), $startDate->format($format));
            }
        } else {
            throw new \Exception("StartDate muest be lees than EndDate");
        }

        //var_dump($this->yAxis);
    }

    public function processObjectCollectionSet($element, $collection, $index, $value, $rangeType = 'day') {
        $this->initXaxis($element, $rangeType);
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

    public function processObjectCollectionSum($element, $collection, $index, $value, $rangeType = 'day') {
        $this->initXaxis($element, $rangeType);
        $methodIndex = "get" . ucfirst($index);
        $methodValue = "get" . ucfirst($value);
        $count = array();
        foreach ($collection as $object) {
            if (method_exists($object, $methodIndex) && method_exists($object, $methodValue)) {

                $y = $this->processReturnY($object->$methodIndex(), $rangeType);


                $x = $object->$methodValue();
                $count[$y] += $x;
            }
        }

        foreach ($count as $key => $val) {
            $this->xAxisAddElement($rangeType, $key, $element, $val);
        }
    }

    protected function initXaxis($element, $rangeType) {
        foreach ($this->yAxis[$rangeType] as $key => $val) {
            if (!is_array($this->xAxis[$element][$rangeType])) {
                $this->xAxis[$element][$rangeType] = array();
            }
            if (!key_exists($key, $this->xAxis[$element][$rangeType])) {
                $this->xAxis[$element][$rangeType][$key] = 0;
            }
        }
    }

    public function processObjectCollectionCount($element, $collection, $index, $rangeType = 'day') {


        $this->initXaxis($element, $rangeType);

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
        $this->initXaxis($element, $rangeType);
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
                    throw new \Exception("Index must be Integer");
                }

                break;
            case "string":
                $y = $valueY;
                if (!is_a($y, "string")) {
                    throw new \Exception("Index must be String");
                }

                break;
            case "day":

                if (is_a($valueY, "DateTime")) {
                    if (isset($this->timeZone)) {
                        $valueY->setTimezone(new \DateTimeZone($this->timeZone));
                    }
                    $y = $valueY->format("Y-m-d");
                } else {
                    throw new \Exception("Index must be Datetime");
                }

                break;
            case "week":

                if (is_a($valueY, "DateTime")) {
                    if (isset($this->timeZone)) {
                        $valueY->setTimezone(new \DateTimeZone($this->timeZone));
                    }
                    $y = $valueY->format("W");
                } else {
                    throw new \Exception("Index must be Datetime");
                }

                break;
            case "month":

                if (is_a($valueY, "DateTime")) {
                    if (isset($this->timeZone)) {
                        $valueY->setTimezone(new \DateTimeZone($this->timeZone));
                    }
                    $y = $valueY->format("Y-m");
                } else {
                    throw new \Exception("Index must be Datetime");
                }

                break;

            default:
                throw new \Exception("rangeType must be defined");
                break;
        }

        return $y;
    }

    public function sumUpToKey($element, $rangeType, $index) {

        foreach ($this->xAxis[$element][$rangeType] as $key => $value) {
            $sum += $value;
            if ($index == $key) {
                break;
            }
        }

        return $sum;
    }

}

?>
