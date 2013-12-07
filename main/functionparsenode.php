<?php

if (isset($_POST['wid']) && isset($_POST['nodeid'])) {


    require 'config.php';
// The file raw.xml contains an XML document with a root element
// and at least an element /[root]/title.




    if (file_exists($file)) {
        $xmll = simplexml_load_file($file);
        if ($xmll === false) {
            echo "parse error";
            exit(0);
        }
        //print_r($xml);
        // echo "<br/><br/>";
    } else {
        exit('Failed to open ' . $file);
    }
    $wid = $_POST['wid'];



    $xml = $xmll->workflow[0];

        $mapping=array();
    foreach ($xml->node as $node) {
        if ($node['id'] != $_POST['nodeid'])
            continue;
        
        $delete = "delete from function_inputs where workflow_id=" . $wid . " and transition_id=" . $_POST['nodeid'];
        if (!mysqli_query($con, $delete)) {
            die('Error :' . mysqli_error($con));
        }
        $result = selectfromdb("function_transition", array("activated", "start_time", "time_limit", "doer"), "workflow_id=" . $wid . " and transition_id=" . $_POST['nodeid']);
        $myrow = mysqli_fetch_array($result);
        $delete = "delete from function_transition where workflow_id=" . $wid . " and id=" . $_POST['nodeid'];
        if (!mysqli_query($con, $delete)) {
            die('Error :' . mysqli_error($con));
        }
        $out = array();
         $fval = array();
        $branch = array();
        $fallback = array();
        $path = array(); //where node has branched
        foreach ($node->outnode as $outnode) {
            array_push($out, (string) $outnode['id']);
        }
        // if(strlen($out))$out=substr($out,0,strlen($out)-1);//removing last comma

        $activated = 0; //status: inactive
        if ($node['type'] == "start") {
            $activated = 1;
        }
        $innodelist = explode(";", (string) ($node->innodes[0]['id']));
        //echo (string)($node->innodes[0]);
        $innodes = array();
        foreach ($innodelist as $inlist) {//array of arrays:: ((5,6),(8),(9,10))
            $l = array();
            $in = explode(",", $inlist);
            foreach ($in as $i) {
                array_push($l, (string) $i);
            }
            array_push($innodes, $l);
        }
        $done_innodes = array();

        //storing inputs and their validation info
        foreach ($node->input as $input) {
            $record = array($wid, $node['id'], $input['type'], $input['name']);
            $vars = array();
            $type = $input['type'];
            foreach ($input->value as $value) {
                $val = array();
                //echo ": ".$value['val']." :<br/>";
                if ((string) $value->get[0] === "") {
                    array_push($val, 0); //value is data
                    array_push($val, (string) $value);
                } else {
                    $v = explode(";", $value->get[0]); //parameters + functionlist
                    $paramlist = explode(":", $v[0]);
                    foreach ($paramlist as $param) {
                        $l = explode(",", $param);
                        if (count($l) == 1) {
                            continue;
                        } else if (count($l) == 2) {//fetch data from previous node
                            array_push($fval, $l);
                        }
                    }
                    array_push($val, 1); //value should be received from function call
                    array_push($val, (string) $value->get[0]);
                }
                if (isset($value["val"])) {
                    array_push($vars, array(0, (string) $value['val'], $val));
                } else if (isset($value["fval"])) {
                    $v = explode(";", $value["fval"]); //parameters + functionlist
                    $paramlist = explode(":", $v[0]);
                    foreach ($paramlist as $param) {
                        $l = explode(",", $param);
                        if (count($l) == 1) {
                            continue;
                        } else if (count($l) == 2) {//fetch data from previous node
                            array_push($fval, $l);
                        }
                    }
                    array_push($vars, array(1, (string) $value['fval'], $val));
                }
            }
            // print_r ($vars);
            //exit(0);
            array_push($record, serialize($vars));
            array_push($record, ""); //chosen value: initially blank
            $validation = array();
            $validation["property"] = array();


            foreach ($input->condition as $condition) {
                if ($condition['type'] == "validation") {
                    $property = $validation['property'];
                    foreach ($condition->check as $check) {
                        $property[(string) $check['property']] = array((string) $check['val'], (string) $check['operator']);
                        // echo "validate:: ".$check['property'].'<br>';
                    }
                    $validation["property"] = $property;
                    //unset($property);
                }
            }
            array_push($record, serialize($validation));
            $editable = (string) $input['editable'];
            if ($editable == "")
                $editable = "false";
            array_push($record, $editable);
            //  array_push($record,serialize($branch));
            if(!isset($node['substitute']))insertintodb("function_inputs", $record, 0);
        }
        //exit(0);
        //storing function calls of the node
        //array(  [data=>array(),functions=>array()], [] .... )
        $functions = array();
        foreach ($node->function as $function) {
            $list = array("data" => array(), "functions" => array(), "branch" => array(), "fallback" => array());
            foreach ($function->data as $data) {
                //   echo ":".$data['type'].":<br>";
                if ((string) $data['type'] === "node") {
                    //        echo "here";
                    $nodeid = explode(",", (string) $data)[0];
                    $inputname = explode(",", (string) $data)[1];
                    array_push($list["data"], array("node", $nodeid, $inputname));
                    array_push($fval, array($nodeid, $inputname));
                } else {
                    array_push($list["data"], array("data", (string) $data));
                }
            }

            foreach ($function->functionname as $func) {
                array_push($list["functions"], (string) $func);
            }
            foreach ($function->condition as $condition) {
                if ($condition['type'] == "branch") {
                    $property = array();
                    foreach ($condition->check as $check) {
                        array_push($property, array((string) $check['property'], (string) $check['val'], (string) $check['operator']));

                        //echo "branch:: ".$check['property'].'<br>';
                    }
                    $outnodes = array();
                    foreach ($condition->outnode as $outnode) {
                        array_push($outnodes, (string) $outnode['id']);
                    }
                    $pair = array();
                    $pair["validation"] = $property;
                    $pair["outnodes"] = $outnodes;
                    array_push($list["branch"], $pair);
                } else if ($condition['type'] == "fallback") {

                    $property = array();
                    foreach ($condition->check as $check) {
                        array_push($property, array((string) $check['property'], (string) $check['val'], (string) $check['operator']));

                        //echo "branch:: ".$check['property'].'<br>';
                    }
                    $outnodes = array();
                    foreach ($condition->outnode as $outnode) {
                        array_push($outnodes, (string) $outnode['id']);
                    }
                    $pair = array();
                    $pair["validation"] = $property;
                    $pair["outnodes"] = $outnodes;
                    array_push($list['fallback'], $pair);
                }
            }
            if ($function['execute'] == "before")
                array_push($functions['before'], $list);
            else
                array_push($functions['after'], $list);
        }

        //branch=array( [ validation=>([inputname,([property,val,operator])], [],[]..),outnodes=>(1,2,3,9) ] , [  ].... )
        foreach ($node->condition as $condition) {
            if ($condition['type'] == "branch") {
                $property = array();
                foreach ($condition->check as $check) {
                    array_push($property, array((string) $check['inputname'], array((string) $check['property'], (string) $check['val'], (string) $check['operator'])));

                    //echo "branch:: ".$check['property'].'<br>';
                }
                $outnodes = array();
                foreach ($condition->outnode as $outnode) {
                    array_push($outnodes, (string) $outnode['id']);
                }
                $pair = array();
                $pair["validation"] = $property;
                $pair["outnodes"] = $outnodes;
                array_push($branch, $pair);
            } else if ($condition['type'] == "fallback") {
                $property = array();
                foreach ($condition->check as $check) {
                    array_push($property, array((string) $check['inputname'], array((string) $check['property'], (string) $check['val'], (string) $check['operator'])));

                    //echo "branch:: ".$check['property'].'<br>';
                }
                $outnodes = array();
                foreach ($condition->outnode as $outnode) {
                    array_push($outnodes, (string) $outnode['id']);
                }
                $pair = array();
                $pair["validation"] = $property;
                $pair["outnodes"] = $outnodes;
                array_push($fallback, $pair);
            }
        }
        $time = (string) $node['time'];
        if ($time == "")
            $time = 50;
        $cost = 0;
        if ($node['cost'] != "")
            $cost = $node['cost'];

        $starttime = time() + 10;
        $record = array($wid, $node['id'], $node['type'], serialize($innodes), serialize($done_innodes), serialize($out), serialize($functions), serialize($branch), serialize($fallback), serialize($path), $myrow['activated'], $myrow['start_time'], $myrow['time_limit'], $myrow['doer'], $cost,serialize($fval));
          if(isset($node['substitute'])){
           $result22=  selectfromdb("function_workflow",array("name","id"),"name='".$node['substitute']."'");
            $row22=  mysqli_fetch_array($result22);
            if($row22!=NULL){
                $mapping[$node['id']]=substitute($record,$row22['id'],$row22['name']);//return array(startnodeid,endnodeid);
            }
            else{insertintodb("function_transition", $record, 1);}
        }
        else insertintodb("function_transition", $record, 1);
        break;
    }
    if(count($mapping))remapworkflow($wid,$mapping);//
    logg("modified function_node wid=".$_POST['wid']." nodeid=".$_POST['nodeid']);
    header('Location: upload_file.php?message="Task Modified successfully"');
//exit(0);
}
?>