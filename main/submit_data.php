<?php

require 'config.php';
if (isset($_POST['wid']) && isset($_POST['nodeid'])) {
    //time check
    $out1=array();
    $where = "id='" . $_POST['nodeid'] . "' and workflow_id='" . $_POST['wid'] . "'";
    $result = selectfromdb("transition", array("start_time", "time_limit", "activated", "cost"), $where);

    //  echo $where;
    $row = mysqli_fetch_array($result);
    $activated = $row['activated'];
    $cost = $row['cost'];
    if ($row == '')
        exit(0);
    /* else if($row['activated']!=1){
      if($row['activated']!=2)echo "This task is not active";
      else echo "This ntask has already been done";
      exit(0);

      } */
    $timeleft = $row['time_limit'] * 60 - (time() - $row['start_time']);
    if ($timeleft < 0) {
        if ($row['activated'] == 1) {
            echo "Oophs! You delayed the task too much. Contact Your Manager<br>";
            $sql = "update transition set activated=3,doer=0 where " . "id=" . $_POST['nodeid'] . " and workflow_id=" . $_POST['wid'];
            //echo $sql."<br>";
            if (!mysqli_query($con, $sql)) {
                die('Error while updating in submit_data.php  ' . ":" . mysqli_error($con));
            }
            exit(0);
        }
    }
    $out2 = array();
    $wfid = $_POST['wid'];
    $changedinputs = array();
    $taskid = $_POST['nodeid'];
    $query = "select name,validation,functions from inputs where workflow_id=$wfid and transition_id=$taskid";

    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_array($result)) {
        if (!isset($_POST[$row['name']]))
            continue;
        $x = $_POST[$row['name']];
        $funct = unserialize($row["functions"]);
        foreach ($funct as $f) {
            $x = callfunc($f, $x);
        }
        $_POST[$row['name']] = $x;


        if ($activated != 1) {
            $query = "select chosen_val from inputs where workflow_id=$wfid and transition_id=$taskid and name='" . $row['name'] . "'";
            $result2 = mysqli_query($con, $query);
            $chosen_val = mysqli_fetch_array($result2)['chosen_val'];

            if ($x != $chosen_val)
                array_push($changedinputs, array($_POST['nodeid'], $row['name']));
        }
        //echo "Value to be checked : ".$x.'<br>';
        $ans = 1;
        $arr = unserialize($row['validation']);
        foreach ($arr as $key => $val) {
            if (count($val) != 0) {
                foreach ($val as $key2 => $val2) {
                    //echo 'Property to be checked : '.$key2.'<br>';
                    //echo 'Value to be checked against: '.$val2[0].'<br>';
                    //echo 'Operator to be used : '.$val2[1].'<br>';
                    $ret = checkcondition($x, $key2, $val2[0], $val2[1], $_POST['wid'], $_POST['nodeid']);
                    echo $ret;
                    if ($ret != "1") {
                        $url = "Location: active_tasks.php?wid=$wfid&nodeid=$taskid&message=$ret";
                        header($url);
                        exit;
                    }
                }
            }
        }
        $result33 = selectfromdb("transition", array("branch"), "workflow_id='" . $_POST['wid'] . "' and id='" . $_POST['nodeid'] . "'");

        $row33 = mysqli_fetch_array($result33);
        $branch = unserialize($row33['branch']);
      foreach ($branch as $br) {

        $validation = $br['validation'];
        $outnodes = $br['outnodes'];
        $fl = "1";
        foreach ($validation as $condition) {
            $name = $condition[0]; //input name
            $property = $condition[1][0];
            $val = $condition[1][1];
            $operator = $condition[1][2];
            $fl = checkcondition($_POST[$name], $property, $val, $operator, $_POST['wid'], $_POST['nodeid']);
            //originalvalue  , property,value tobe compared with, operator
            if ($fl != "1")
                break; //conditions dont hold
        }
        if ($fl == "1") {
            // echo"here2";exit(0);
            foreach ($outnodes as $i) {
                if (!in_array($i, $out1))
                    array_push($out1, $i);
            }
        }
    }
        $update = "UPDATE inputs SET chosen_val='" . $_POST[$row['name']] . "' WHERE transition_id='" . $_POST['nodeid'] . "' AND workflow_id=" . $_POST['wid'] . " AND name='" . $row['name'] . "'";
        mysqli_query($con, $update);
    }
    //executing after functions

    $result = selectfromdb("transition", array("functions"), "workflow_id='" . $_POST['wid'] . "' and id='" . $_POST['nodeid'] . "'");
    $list = unserialize(mysqli_fetch_array($result)['functions']);
    $list = $list['after']; //functions to be executed after data has been submitted

    foreach ($list as $pair) {//pair has parameter +functionlist + branch + fallback
        //echo "here";exit(0);
        $parameters = array();
        $data = $pair["data"];
        $functionlist = $pair["functions"];
        $branch = $pair["branch"];
        $fallback = $pair["fallback"];
        foreach ($data as $param) {
            //echo $param[0] . " -------------------<br>";
            if ($param[0] === "data")
                array_push($parameters, $param[1]);
            else if ($param[0] === "node") {
                //print_r($param);echo "<br>";
                if ($param[1] != $_POST['nodeid']) {


                    $result2 = selectfromdb("inputs", array("chosen_val"), "name='" . $param[2] . "' and transition_id='" . $param[1] . "' and workflow_id='" . $_POST['wid'] . "'");
                    $row2 = mysqli_fetch_array($result2);
                    $chosenval = "";
                    if (isset($_POST[$param[2]]))
                        $chosenval = $_POST[$param[2]]; //was not a resubmission with editable =false
                    else
                        $chosenval = $row2['chosen_val'];
                    //echo ":".$row2['chosen_val'].":<br>";
                    array_push($parameters, $chosenval);
                } else {
                    if (isset($_POST[$param[2]]))
                        array_push($parameters, $_POST[$param[2]]);
                    else {//after ist submissions some fields may not get submitted as ther are disabled
                        $result2 = selectfromdb("inputs", array("chosen_val"), "name='" . $param[2] . "' and transition_id='" . $param[1] . "' and workflow_id='" . $_POST['wid'] . "'");
                        $row2 = mysqli_fetch_array($result2);
                        //echo ":".$row2['chosen_val'].":<br>";
                        array_push($parameters, $row2['chosen_val']);
                    }
                }
            } else if ($param[0] === "table") {
                $table = $param[1];
                $field = $param[2];
                $conditions = $param[3];
                $condition = "";
                $ct = count($conditions);
                $i = 0;
                foreach ($conditions as $cond) {
                    $i+=1;
                    $condition.=$cond[0];
                    $v = explode(",", $cond[1]);
                    if (count($v) == 1) {//data
                        $condition.="'" . $cond[1] . "' ";
                        if ($i != $ct) {
                            $condition.="and ";
                        }
                    } else {//from node
                        $result2 = selectfromdb("inputs", array("chosen_val"), "name='" . $v[1] . "' and transition_id='" . $v[0] . "' and workflow_id='" . $_GET['wid'] . "'");
                        $row2 = mysqli_fetch_array($result2);
                        $condition.="'" . $row['chosen_val'] . "' ";

                        if ($i != $ct) {
                            $condition.="and ";
                        }
                    }
                }
                array_push($parameters, mysqli_fetch_array(selectfromdb($table, array($field), $condition))[$field]);
            }
        }
        if (count($functionlist) == 0) {
            $a = "";
            foreach ($parameter as $p)
                $a.=$p;
            $parameter = $a;
        }
        else
            foreach ($functionlist as $function) {
                $parameters = callfunc($function, $parameters);
                // echo "here--------------:" . $function . ":<br>";
                //----------------------------------
                //if there is an error and a fallback condition has been added
                //print_r($fallback);
                ///echo "-------------------------------------------------: ".$fallback.":<br>";
                // exit(0);
            }

        //  echo "function result:" ;print_r($parameters);echo"<br>";exit(0);

        foreach ($fallback as $br) {
            //  e
            // echo "here--------------:" . $parameters . ":<br>   ";
            $validation = $br['validation'];
            $outnodes = $br['outnodes'];
            $fl = "1";
            foreach ($validation as $condition) {

                $property = $condition[0];
                $val = $condition[1];
                $operator = $condition[2];
                $fl = checkcondition($parameters, $property, $val, $operator, $_POST['wid'], $_POST['nodeid']);
                //originalvalue  , property,value tobe compared with, operator
                if ($fl != "1")
                    break; //conditions dont hold
            }
            if ($fl == "1") {
                fallback($_POST['wid'], $_POST['nodeid'], array(), $outnodes); //visited array,outnode array where we havre to move
                // echo"ess"; exit(0);
                if ($user_id != 0) {
                    if ($user_status == "manager") {
                        header('Location: upload_file.php?message="Oophs! There was some Error!"');
                    }
                    else
                        header('Location: active_tasks.php');
                }
                else
                    header('Location: login.php');
                exit(0);
            }

            //----------------------------------
        }

        foreach ($branch as $br) {

            $validation = $br['validation'];
            $outnodes = $br['outnodes'];
            $fl = "1";
            foreach ($validation as $condition) {

                $property = $condition[0];
                $val = $condition[1];
                $operator = $condition[2];
                if ($property == "ifchanged")
                    continue;
                $fl = checkcondition($parameters, $property, $val, $operator, $_POST['wid'], $_POST['nodeid']);
                //originalvalue  , property,value tobe compared with, operator
                if ($fl != "1")
                    break; //conditions dont hold
            }
            if ($fl == "1") {
                foreach ($outnodes as $i) {

                    array_push($out2, $i);
                }
            }
        }
    }
    // exit(0);
    //updating active states of nodes
    $result = selectfromdb("transition", array("outnodes", "branch", "path", "fallback"), "workflow_id='" . $_POST['wid'] . "' and id='" . $_POST['nodeid'] . "'");

    $row = mysqli_fetch_array($result);

    //outnode: array(nodeid1,nodeid2,..)
    $out = unserialize($row['outnodes']);
    // print_r($out);exit(0);
    foreach ($out2 as $outnode)
        if (!in_array($outnode, $out))
            array_push($out, $outnode);
    foreach ($out1 as $outnode)
        if (!in_array($outnode, $out))
            array_push($out, $outnode);

    $path = unserialize($row['path']);
    //print_r($out);
    // print_r($path);
    //  exit(0);
    $branch = unserialize($row['branch']);
    $fallback = unserialize($row['fallback']);
    foreach ($fallback as $br) {
        $validation = $br['validation'];
        $outnodes = $br['outnodes'];
        $fl = "1";
        foreach ($validation as $condition) {
            $name = $condition[0]; //input name
            $property = $condition[1][0];
            $val = $condition[1][1];
            $operator = $condition[1][2];
            $fl = checkcondition($_POST[$name], $property, $val, $operator, $_POST['wid'], $_POST['nodeid']);
            //originalvalue  , property,value tobe compared with, operator
            if ($fl != "1")
                break; //conditions dont hold
        }
        if ($fl == "1") {
            //print_r($outnodes); exit(0);
            fallback($_POST['wid'], $_POST['nodeid'], array(), $outnodes); //visited array,outnode array where we havre to move

            if ($user_id != 0) {
                if ($user_status == "manager") {
                    header('Location: upload_file.php?message="Oophs! There was some Error! Fallback triggered"');
                }
                else
                    header('Location: active_tasks.php?message="Ooophs! There was some Error! Fallback triggered"');
            }
            else
                header('Location: login.php');
            exit(0);
        }
    }

    foreach ($branch as $br) {

        $validation = $br['validation'];
        $outnodes = $br['outnodes'];
        $fl = "1";
        foreach ($validation as $condition) {
            $name = $condition[0]; //input name
            $property = $condition[1][0];
            $val = $condition[1][1];
            $operator = $condition[1][2];
            $fl = checkcondition($_POST[$name], $property, $val, $operator, $_POST['wid'], $_POST['nodeid']);
            //originalvalue  , property,value tobe compared with, operator
            if ($fl != "1")
                break; //conditions dont hold
        }
        if ($fl == "1") {
            // echo"here2";exit(0);
            foreach ($outnodes as $i) {
                if (!in_array($i, $out))
                    array_push($out, $i);
            }
        }
    }
    if ($activated == "2") {
        // print_r($changedinputs);exit(0);
        // print_r($out);exit(0);
        deactivate($_POST['wid'], $_POST['nodeid'], $changedinputs, 0, array($_POST['nodeid'])); //input resubmitted so deactive node dependent on it
        logg("modified input wid=" . $_POST['wid'] . " nodeid=" . $_POST['nodeid']);
        //exit(0);
    }
    $deactivate = "UPDATE transition SET activated='2' WHERE id='" . $_POST['nodeid'] . "' AND workflow_id=" . $_POST['wid']; //setting done
    echo $deactivate . "<br>";
    if (!mysqli_query($con, $deactivate)) {
        die('Error  : ' . mysqli_error($con));
    }

    foreach ($out as $node) {

        echo "node:" . $node . "<br>";
        $result1 = selectfromdb("transition", array("innodes", "done_innodes", "mustbedeactive"), "workflow_id='" . $_POST['wid'] . "' and id='" . $node . "'");
        $row1 = mysqli_fetch_array($result1);
        $doneinnodes = unserialize($row1['done_innodes']);
        print_r($doneinnodes);
        echo " doneinnodes<br>";
        $innodes = unserialize($row1['innodes']);
        // print_r($doneinnodes);echo " :doneinnodes<br>";
        //print_r($innodes);
        if (!in_array($_POST['nodeid'], $doneinnodes)) {
            //     print_r($out);
            //echo"here";exit(0);

            array_push($doneinnodes, $_POST['nodeid']);
            if (!in_array($node, $path))
                array_push($path, $node);
            $update = "UPDATE transition SET done_innodes='" . serialize($doneinnodes) . "' WHERE id='" . $node . "' AND workflow_id=" . $_POST['wid'];
            mysqli_query($con, $update);
            //       echo "innodes array: "; print_r($innodes); echo "<br>";
            foreach ($innodes as $list) {
                $fl = 1;
                //  print_r($list);
                echo " :list<br>";
                foreach ($list as $outnode) {
                    if (!in_array($outnode, $doneinnodes)) {
                        $fl = 0;
                        break;
                    }
                }
                if ($fl) {

                    $lst = unserialize($row1["mustbedeactive"]);
                    //echo"lst:: ";print_r($lst);exit(0);
                    foreach ($lst as $id) {
                        /*   if (mysqli_fetch_array(selectfromdb("transition", array("activated"), "id='" . $id . "' AND workflow_id=" . $_POST['wid']))['activated'] == 1) {
                          $fl = 0;
                          break;
                          } */
                        $deactivate = "UPDATE transition SET activated='0' WHERE id='" . $id . "' AND workflow_id=" . $_POST['wid'];
                        // $sql = "update transition set start_time='" . time() . "' where " . "id=" . $node . " and workflow_id=" . $_POST['wid'];
                        if (!mysqli_query($con, $deactivate)) {
                            die('Error ' . mysqli_error($con));
                        }
                    }
                }
                if ($fl) {
                    // echo "asdadadadadaad :::<br>";

                    $activate = "UPDATE transition SET activated='1' WHERE id='" . $node . "' AND workflow_id=" . $_POST['wid'];
                    $sql = "update transition set start_time='" . time() . "' where " . "id=" . $node . " and workflow_id=" . $_POST['wid'];
                    mysqli_query($con, $sql);
                    //  echo $activate."<br>";
                    mysqli_query($con, $activate);

                    //                    echo "node=".$node."<br>";
                }
            }
        }
    }

    echo"here2 " . $_POST['nodeid'];
    print_r($path);
    //   exit(0);
    //2:done but active  3:delayed 1:active 0:not started 4:done and inactive
    mysqli_query($con, "UPDATE transition SET path='" . serialize($path) . "' WHERE id='" . $_POST['nodeid'] . "' AND workflow_id=" . $_POST['wid']);

    $result = selectfromdb("workflow", array("cost"), "id=" . $_POST['wid']);
    $cost = (int) $cost;
    // echo "<br>".$cost;
    $cost+=(int) mysqli_fetch_array($result)['cost'];
    // echo "<br>".$cost;
    //exit(0);
    if (!mysqli_query($con, "update workflow set cost=" . $cost . " WHERE id=" . $_POST['wid'])) {
        die('Error while updating cost : ' . mysqli_error($con));
    }

    logg("entered input wid=" . $_POST['wid'] . " nodeid=" . $_POST['nodeid']);

    header('Location: active_tasks.php');
}
?>
