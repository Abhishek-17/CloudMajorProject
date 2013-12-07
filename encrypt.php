<?php
	//encrypr
	function change_import($filename,$array,$key=""){
		$str="";
		//echo "filename n encrypt.php=".$filename,"<br>";exit(0);
		$file=fopen($filename,"r");
		while(!feof($file)){
			$line=fgets($file);
			if(strpos($line,'public void readFields(ResultSet __dbResults)')!==false){
				$str.=$line;
				
				while(1){
					$line=fgets($file);
					if(strpos($line,'}')!==false){
						//logic
						foreach($array as $col){
							if($col=="")continue;
							$st="this.".$col."="."CipherUtils.encrypt(this.".$col.',"'.$key.'"'.").trim();\n";
							$str.=$st;
						}
						break;
					}
					else $str.=$line;
				}
			}
			else if(strpos($line,'public class login')!==false){
				$str.="import com.scoopEnhancements.CipherUtils;\n";

			}
			$str.=$line;
		}
		fclose($file);
		$str="///// \n".$str;
		file_put_contents($filename, $str);
	}
	
	//decrypt
	function change_export($filename,$array,$key){
		$str="";
		$file=fopen($filename,"r");
		while(!feof($file)){
			$line=fgets($file);
			if(strpos($line,'public int write(PreparedStatement __dbStmt')!==false){
						//logic
				$str.=$line;
				foreach($array as $col){
					if($col=="")continue;
						$col.="="."CipherUtils.decrypt(".$col.',"'.$key.'"'.").trim();\n";
						$str.=$col;
				}
				continue;
					
			}
			if(strpos($line,'public class login')!==false){
				$str.="import com.scoopEnhancements.CipherUtils;\n";

			}
			$str.=$line;
		}
		fclose($file);
	//	$str="///// \n".$str;
		file_put_contents($filename, $str);
	}
	
?>