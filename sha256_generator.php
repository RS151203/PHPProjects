<?php
$filename_array=[];
$hash_array=[];
	function folder_contents_checksum($folder_name){
		global $filename_array;
		global $hash_array;
		foreach(glob("$folder_name/*") as $file)
		{
			if (is_dir($file)==True){
				folder_contents_checksum($file);
			}
			else{
                		array_push($filename_array,$file);
				array_push($hash_array,hash_file("sha256","$file"));
			}
		}

	}

	$file_location=$_POST["path_to_file_or_folder"];
	
	global $filename_array;
	global $hash_array;
	if (file_exists($file_location)==False){
		
		echo "$file_location entered file/folder location doesn't exist.Please check your entry";
		
	}
	else{
		if (is_dir($file_location)==False){
                	array_push($filename_array,$file_location);
                	array_push($hash_array,hash_file("sha256","$file_location"));

		}
		else{
			foreach(glob("$file_location/*") as $file)
           			 {
               				if (is_dir($file)==True){
                   				 folder_contents_checksum($file);
               					 }
                			else{
		                               	 array_push($filename_array,$file);
                		                 array_push($hash_array,hash_file("sha256","$file"));
			                    }
           			 }
		}
	}

        
	$hash_and_file_location=[];
	$final_output="";
	$output_file_name=$_POST["download_file_name"];

        $temp_dir=sys_get_temp_dir();
        $foldername="uploads";
        chdir($temp_dir);

        if (strtoupper(substr(php_uname(), 0, 3)) === 'WIN') {
                $uploaded_files_folder="$temp_dir\\$foldername";
        }
        else if (strtoupper(substr(php_uname(), 0, 3)) === 'LIN'){
                $uploaded_files_folder="$temp_dir/$foldername";
        }
        else if (strtoupper(substr(php_uname(), 0, 3)) === 'MAC'){
                $uploaded_files_folder="$temp_dir/$foldername";
	}

	chdir($foldername);

       	for($m=0;$m<=count($hash_array)-1;$m++){
        	$final_output= $final_output."$filename_array[$m];$hash_array[$m]\n";
	}

	$output_file=fopen("$output_file_name.txt","w") or die("Unable to open file!");
	if (strtoupper(substr(php_uname(), 0, 3)) == 'WIN') {
		$final_output=str_replace("/","\\",$final_output);
	}
	fwrite($output_file,$final_output);
	fclose($output_file);

	$file_to_download = "$output_file_name.txt";

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
                rmrf($uploaded_files_folder);
        
        }	

	
?>	
