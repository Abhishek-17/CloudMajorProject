
<?php

if (isset($_POST['wid'])) {


    require 'config.php';
// The file raw.xml contains an XML document with a root element
// and at least an element /[root]/title.




    if (file_exists($file)) {
        $xmll = simplexml_load_file($file);
        if ($xmll === false)
            exit(0);
        //print_r($xml);
        // echo "<br/><br/>";
    } else {
        exit('Failed to open ' . $file);
    }
    $wid = $_POST['wid'];
    $result = selectfromdb("function_workflow", array("parent_id"), "id='" . $wid . "'");
    $row = mysqli_fetch_array($result)['parent_id'];
    if ($row == "") {
        echo "error in functionworparseworkflow.php";
        exit(0);
    }
    $xml = $xmll->workflow[0];
    $res=  selectfromdb("function_workflow",array("id"),"name='".(string) $xml['name']."'");
    $rr=  mysqli_fetch_array($res);
    if($rr!=NULL){
        echo "function with same name already exists.";
        exit(0);
    }
    $pwid = $row['parent_id'];
    $delete = "delete from function_workflow where id=" . $wid;
    if (!mysqli_query($con, $delete)) {
        die('Error :' . mysqli_error($con));
    }
    $delete = "delete from function_transition where workflow_id=" . $wid;
    if (!mysqli_query($con, $delete)) {
        die('Error :' . mysqli_error($con));
    }
    $delete = "delete from function_inputs where workflow_id=" . $wid;
    if (!mysqli_query($con, $delete)) {
        die('Error :' . mysqli_error($con));
    }
    $mapping=array();
    
    $workflow = array($wid, $pwid, (string) $xml['name'], "ist work flow", date("Y/m/d"), date("Y/m/d"), $user_id, 0);
//echo  (string)$xml['name'].'<br>'; exit(0);
//echo  (string)$xml->node[0]['id'];

    insertintodb("function_workflow", $workflow, 1);
    //$update="update workflow set name=".$xml['name'].",workflow_desc="."ist workflow".",start_date=".date("Y/m/d").",end_date=".date("Y/m/d").",manager_id=".$user_id." where id='".$_POST['wid']."'";
    // echo $update;
    //  exit(0);
//exit(0);


    foreach ($xml->node as $node) {
        $out = array();
        $branch = array();
         $fval = array();
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
           if(!isset($node['substitute'])) insertintodb("function_inputs", $record, 0);
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
        $record = array($wid, $node['id'], $node['type'], serialize($innodes), serialize($done_innodes), serialize($out), serialize($functions), serialize($branch), serialize($fallback), serialize($path), $activated, $starttime, $time, 0, $cost,serialize($fval));
         if(isset($node['substitute'])){
           $result22=  selectfromdb("function_workflow",array("name","id"),"name='".$node['substitute']."'");
            $row22=  mysqli_fetch_array($result22);
            if($row22!=NULL){
                $mapping[$node['id']]=substitute($record,$row22['id'],$row22['name']);//return array(startnodeid,endnodeid);
            }
            else{insertintodb("function_transition", $record, 1);}
        }
        else insertintodb("function_transition", $record, 1);
    }
    if(count($mapping))remapworkflow($wid,$mapping);//
       logg("modified function_workflow wid=".$_POST['wid']);
    header('Location: upload_file.php?message="Workflow Modified successfully"');
//exit(0);
}
?>
