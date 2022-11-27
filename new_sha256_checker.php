<?php
        function rmrf($dir){
                foreach (glob($dir) as $file) {
                        if (is_dir($file)) {
                                rmrf("$file/*");
                                rmdir($file);
                        } else {
                                unlink($file);
                        }
                }
        }
	function name_splitter($file_name){
		$filename_array=[];
		$hash_array=[];
		$handle = fopen($file_name, "r");
		if ($handle) {
    			while (($line = fgets($handle)) !== false){
				preg_match("/(.*);(.*)/i",$line,$output);
				array_push($filename_array,$output[1]);
				array_push($hash_array,$output[2]);
    			}
    			fclose($handle);
		}
		$filename_hash_array=[$filename_array,$hash_array];
		return $filename_hash_array;
	}

	function delete_similar_items($items_to_delete,$to_delete_from){
		foreach($items_to_delete as $items){
			unset($to_delete_from[$items]);
		}
		return $to_delete_from;
	}

	function final_textfile_array($textfile,$textfile_name_array,$textfile_hash_array){
		$file_array=[];
		if($textfile=="Text_file1"){
			for($m=0;$m<=count($textfile_hash_array)-1;$m++){
				array_push($file_array,"$textfile_name_array[$m],$textfile_hash_array[$m],NULL,NULL");

			}
		}
		elseif($textfile=="Text_file2"){
			for($m=0;$m<=count($textfile_hash_array)-1;$m++){
				array_push($file_array,"NULL,NULL,$textfile_name_array[$m],$textfile_hash_array[$m]");	
			}
		
		}
		else{
			echo "SOME ERROR HAS OCCURED!";
		}
		return $file_array;
	}

	function hash_check($text_file1_name_array,$text_file1_hash_array,$text_file2_name_array,$text_file2_hash_array){
		$similar_hash_only_array=[];
		$similar_name_only_array=[];
		$similar_hash_and_name_array=[];
		$text_file2_number_array=[];
		$text_file1_number_array=[];
		$text_file2_array=[];
		$text_file1_array=[];
		if(count($text_file2_hash_array)>=count($text_file1_hash_array)){
			for($i=0;$i<=count($text_file2_hash_array)-1;$i++){
				for($j=0;$j<=count($text_file1_hash_array)-1;$j++){
					if($text_file2_hash_array[$i]==$text_file1_hash_array[$j]){
						if (strtoupper(substr(php_uname(), 0, 3)) == 'WIN') {
							$file2_name=explode("\\",$text_file2_name_array[$i]);
							$file2=count($file2_name)-1;
							$file1_name=explode("\\",$text_file1_name_array[$j]);
							$file1=count($file1_name)-1;
						}
						else if (strtoupper(substr(php_uname(), 0, 3)) == 'LIN'){
							$file2_name=explode("/",$text_file2_name_array[$i]);
							$file2=count($file2_name)-1;
							$file1_name=explode("/",$text_file1_name_array[$j]);
							$file1=count($file1_name)-1;
						}
						else if (strtoupper(substr(php_uname(), 0, 3)) == 'MAC'){
							$file2_name=explode("/",$text_file2_name_array[$i]);
							$file2=count($file2_name)-1;
							$file1_name=explode("/",$text_file1_name_array[$j]);
							$file1=count($file1_name)-1;
						}
						else{
						}
						if($file2_name[count($file2_name)-1]==$file1_name[count($file1_name)-1]){
							array_push($similar_hash_and_name_array,"$text_file1_name_array[$j],$text_file1_hash_array[$j],$text_file2_name_array[$i],$text_file2_hash_array[$i]");
							array_push($text_file2_number_array,$i);
							array_push($text_file1_number_array,$j);
							break;
						}
						else{
							array_push($similar_hash_only_array,"$text_file1_name_array[$j],$text_file1_hash_array[$j],$text_file2_name_array[$i],$text_file2_hash_array[$i]");
							array_push($text_file2_number_array,$i);
							array_push($text_file1_number_array,$j);
							break;
						}
					}
					else{
						if (strtoupper(substr(php_uname(), 0, 3)) == 'WIN') {
							$file2_name=explode("\\",$text_file2_name_array[$i]);
							$file2=count($file2_name)-1;
							$file1_name=explode("\\",$text_file1_name_array[$j]);
							$file1=count($file1_name)-1;
						}
						else if (strtoupper(substr(php_uname(), 0, 3)) == 'LIN'){
							$file2_name=explode("/",$text_file2_name_array[$i]);
							$file2=count($file2_name)-1;
							$file1_name=explode("/",$text_file1_name_array[$j]);
							$file1=count($file1_name)-1;
						}
						else if (strtoupper(substr(php_uname(), 0, 3)) == 'MAC'){
							$file2_name=explode("/",$text_file2_name_array[$i]);
							$file2=count($file2_name)-1;
							$file1_name=explode("/",$text_file1_name_array[$j]);
							$file1=count($file1_name)-1;
						}
						else{
						}
						if($file2_name[count($file2_name)-1]==$file1_name[count($file1_name)-1]){
							array_push($similar_name_only_array,"$text_file1_name_array[$j],$text_file1_hash_array[$j],$text_file2_name_array[$i],$text_file2_hash_array[$i]");
							array_push($text_file2_number_array,$i);
							array_push($text_file1_number_array,$j);
							break;
						}
						else{
							continue;
						}
						continue;
					}
				}
			}
			$text_file2_hash_array=array_values(delete_similar_items($text_file2_number_array,$text_file2_hash_array));
			$text_file2_name_array=array_values(delete_similar_items($text_file2_number_array,$text_file2_name_array));
			$text_file1_hash_array=array_values(delete_similar_items($text_file1_number_array,$text_file1_hash_array));
			$text_file1_name_array=array_values(delete_similar_items($text_file1_number_array,$text_file1_name_array));
		}
		else{
			for($i=0;$i<=count($text_file1_hash_array)-1;$i++){
				for($j=0;$j<=count($text_file2_hash_array)-1;$j++){
					if($text_file1_hash_array[$i]==$text_file2_hash_array[$j]){
						if (strtoupper(substr(php_uname(), 0, 3)) == 'WIN') {
							$file2_name=explode("\\",$text_file2_name_array[$j]);
							$file2=count($file2_name)-1;
							$file1_name=explode("\\",$text_file1_name_array[$i]);
							$file1=count($file1_name)-1;
						}
						else if (strtoupper(substr(php_uname(), 0, 3)) == 'LIN'){
							$file2_name=explode("/",$text_file2_name_array[$j]);
							$file2=count($file2_name)-1;
							$file1_name=explode("/",$text_file1_name_array[$i]);
							$file1=count($file1_name)-1;
						}
						else if (strtoupper(substr(php_uname(), 0, 3)) == 'MAC'){
							$file2_name=explode("/",$text_file2_name_array[$j]);
							$file2=count($file2_name)-1;
							$file1_name=explode("/",$text_file1_name_array[$i]);
							$file1=count($file1_name)-1;
						}
						if($file1_name[$file1]==$file2_name[$file2]){
							array_push($similar_hash_and_name_array,"$text_file1_name_array[$i],$text_file1_hash_array[$i],$text_file2_name_array[$j],$text_file2_hash_array[$j]");
							array_push($text_file1_number_array,$i);
							array_push($text_file2_number_array,$j);
							break;
						}
						else{
							array_push($similar_hash_only_array,"$text_file1_name_array[$i],$text_file1_hash_array[$i],$text_file2_name_array[$j],$text_file2_hash_array[$j]");
							array_push($text_file1_number_array,$i);
							array_push($text_file2_number_array,$j);
							break;
						}
					}
					else{
						if (strtoupper(substr(php_uname(), 0, 3)) == 'WIN') {
							$file2_name=explode("\\",$text_file2_name_array[$j]);
							$file2=count($file2_name)-1;
							$file1_name=explode("\\",$text_file1_name_array[$i]);
							$file1=count($file1_name)-1;
						}
						else if (strtoupper(substr(php_uname(), 0, 3)) == 'LIN'){
							$file2_name=explode("/",$text_file2_name_array[$j]);
							$file2=count($file2_name)-1;
							$file1_name=explode("/",$text_file1_name_array[$i]);
							$file1=count($file1_name)-1;
						}
						else if (strtoupper(substr(php_uname(), 0, 3)) == 'MAC'){
							$file2_name=explode("/",$text_file2_name_array[$j]);
							$file2=count($file2_name)-1;
							$file1_name=explode("/",$text_file1_name_array[$i]);
							$file1=count($file1_name)-1;
						}
						if($file1_name[$file1]==$file2_name[$file2]){
							array_push($similar_name_only_array,"$text_file1_name_array[$i],$text_file1_hash_array[$i],$text_file2_name_array[$j],$text_file2_hash_array[$j]");
							array_push($text_file1_number_array,$i);
							array_push($text_file2_number_array,$j);
							break;
						}
						else{
							continue;
						}
						continue;
					}
				}
			}
			$text_file1_hash_array=array_values(delete_similar_items($text_file1_number_array,$text_file1_hash_array));
			$text_file1_name_array=array_values(delete_similar_items($text_file1_number_array,$text_file1_name_array));
			$text_file2_hash_array=array_values(delete_similar_items($text_file2_number_array,$text_file2_hash_array));
			$text_file2_name_array=array_values(delete_similar_items($text_file2_number_array,$text_file2_name_array));
		}
		$text_file1_array=final_textfile_array("Text_file1",$text_file1_name_array,$text_file1_hash_array);
		$text_file2_array=final_textfile_array("Text_file2",$text_file2_name_array,$text_file2_hash_array);

		$final_array=[$text_file1_array,$text_file2_array,$similar_name_only_array,$similar_hash_only_array,$similar_hash_and_name_array];
		
		return $final_array;
	}

        $temp_dir=sys_get_temp_dir();
        $foldername="uploads";
        chdir($temp_dir);

        if (strtoupper(substr(php_uname(), 0, 3)) == 'WIN') {
                $uploaded_files_folder="$temp_dir\\$foldername";
        }
        else if (strtoupper(substr(php_uname(), 0, 3)) == 'LIN'){
                $uploaded_files_folder="$temp_dir/$foldername";
        }
        else if (strtoupper(substr(php_uname(), 0, 3)) == 'MAC'){
                $uploaded_files_folder="$temp_dir/$foldername";
        }

        if(is_dir($foldername)==True){
		rmrf("$uploaded_files_folder");
        }

	mkdir($foldername);
        chdir($foldername);
        $name_of_file1=$_FILES["file1_upload"]["name"];
        $name_of_file2=$_FILES["file2_upload"]["name"];
        $tmp_path_of_file1=$_FILES["file1_upload"]["tmp_name"];
        $tmp_path_of_file2=$_FILES["file2_upload"]["tmp_name"];

        if($name_of_file1!=""){

                move_uploaded_file($tmp_path_of_file1,"$uploaded_files_folder/$name_of_file1");
        }
        else{
                echo "FILE1 NOT UPLOADED";
        }
        if($name_of_file2!=""){

                move_uploaded_file($tmp_path_of_file2,"$uploaded_files_folder/$name_of_file2");
        }
        else{
                echo "FILE2 NOT UPLOADED";
	}

        $text_file1_filename=$name_of_file1;
        $text_file2_filename=$name_of_file2;
   
	$text_file1_array=name_splitter($text_file1_filename);
	$text_file2_array=name_splitter($text_file2_filename);
	
	$final_array=hash_check($text_file1_array[0],$text_file1_array[1],$text_file2_array[0],$text_file2_array[1]);
	
	function write_into_file($array_name,$file,$tag){

		foreach($array_name as $elements){
			fwrite($file,"$tag,$elements\n");
		}
	}

	$filename=$_POST["download_file_name"];
	$file=fopen("$filename.csv","w+") or die("Unable to open file");
	fwrite($file,"Tag,File1Name,File1Hash,File2Name,File2Hash\n");
	write_into_file($final_array[0],$file,"NOTINTEST");
	write_into_file($final_array[1],$file,"NOTINPROD");
	write_into_file($final_array[2],$file,"SAMENAMEDIFFERHASH");
	write_into_file($final_array[3],$file,"SAMEHASHDIFFERNAME");
	write_into_file($final_array[4],$file,"SAMEHASHSAMENAME");
	fclose($file);

	
	$file_to_download="$filename.csv";

	if (file_exists($file_to_download)) {
    		header('Content-Description: File Transfer');
    		header('Content-Type: application/octet-stream');
	    	header('Content-Disposition: attachment; filename="'.basename($file_to_download).'"');
		header('Expires: 0');
    		header('Cache-Control: must-revalidate');
    		header('Pragma: public');
    		header('Content-Length: ' . filesize($file_to_download));
	    	readfile($file_to_download);
    		exit;
	}

        if(is_dir($foldername)==True){
		rmrf("$uploaded_files_folder");
        }


?>
