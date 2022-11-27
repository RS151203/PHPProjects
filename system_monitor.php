<?php
function delete_temp_file()
{
    unlink("port_80.txt");
    unlink("port_443.txt");
    unlink("apache_mem.txt");
}

if (strtoupper(substr(php_uname(), 0, 3)) == 'WIN') {
    chdir(sys_get_temp_dir());
    if (file_exists("port_80.txt") == 1) {
        unlink("port_80.txt");
    }
    if (file_exists("port_443.txt") == 1) {
        unlink("port_443.txt");
    }
    if (file_exists("apache_mem.txt") == 1) {
        unlink("apache_mem.txt");
    }
    $cpu_name_cmd = shell_exec("wmic path win32_Processor get Name");
    $cpu_name_arr = explode("\n", $cpu_name_cmd);
    $cpu_name = $cpu_name_arr[1];
    $cpu_cores_cmd = shell_exec("wmic path win32_Processor get NumberOfCores");
    $cpu_cores_arr = explode("\n", $cpu_cores_cmd);
    $cpu_cores = $cpu_cores_arr[1];
    $cpu_threads_cmd = shell_exec("wmic path win32_Processor get NumberOfLogicalProcessors");
    $cpu_threads_arr = explode("\n", $cpu_threads_cmd);
    $cpu_threads = $cpu_threads_arr[1];
    $cpu_load_cmd = shell_exec("typeperf -sc 1 \"\\processor(_total)\\% processor time\"");
    $cpu_load_arr = explode(",", $cpu_load_cmd);
    $cpu_load_arr_len = count($cpu_load_arr) - 2;
    $new_cpu_load_arr = explode("\"", $cpu_load_arr[$cpu_load_arr_len]);
    $new_cpu_load = $new_cpu_load_arr[1];
    $cpu_load = round($new_cpu_load, 2);


    $mem_total_cmd = str_replace(" ", "", shell_exec("wmic OS get TotalVisibleMemorySize /Value"));
    $mem_aval_cmd = str_replace(" ", "", shell_exec("wmic OS get FreePhysicalMemory /Value"));
    $mem_total_arr = explode("=", $mem_total_cmd);
    $mem_aval_arr = explode("=", $mem_aval_cmd);
    $mem_total = intval($mem_total_arr[1]);
    $mem_aval = intval($mem_aval_arr[1]);
    $mem_used = $mem_total - $mem_aval;

    try {
        $mem_used_apache_cmd = shell_exec("for /f \"tokens=5\" %i in ('tasklist ^| findstr \"httpd\"') do @echo %i >> apache_mem.txt");
    } catch (Exception $except) {
        $mem_used_apache_cmd = shell_exec("for /f \"tokens=5\" %i in ('tasklist ^| findstr \"apache\"') do @echo %i >> apache_mem.txt");
    }

    $file = fopen("apache_mem.txt", "r");
    $mem_used_apache = 0;
    while (!feof($file)) {
        $line = fgets($file);
        $number = str_replace(",", "", $line);
        $mem_used_apache = $mem_used_apache + intval($number);
    }
    fclose($file);
    $mem_used_apache = round(($mem_used_apache / 1024), 2) . " Mb";
    $mem_percent = round(($mem_used / $mem_total) * 100, 2);

    $disk_space_total = round(disk_total_space("C:") / (1024 ** 3), 2);
    $disk_space_aval = round(disk_free_space("C:") / (1024 ** 3), 2);
    $disk_space_used = $disk_space_total - $disk_space_aval;
    $disk_percent = round(($disk_space_used / $disk_space_total) * 100, 2);

    $port_80 = shell_exec("for /f \"tokens=2,4\" %i in ('netstat -an ^| findstr \"ESTABLISHED\"')do @echo %i  | findstr \":80\" >> port_80.txt");
    $port_443 = shell_exec("for /f \"tokens=2,4\" %i in ('netstat -an ^| findstr \"ESTABLISHED\"') do @echo %i  | findstr \":443\" >> port_443.txt");

    $port_80_lines = count(file("port_80.txt"));
    $port_443_lines = count(file("port_443.txt"));

    delete_temp_file();

    $active_users = $port_80_lines + $port_443_lines - 1;
} else if (strtoupper(substr(php_uname(), 0, 3)) == 'LIN') {

    $cpu_name = shell_exec("grep -i 'model name' /proc/cpuinfo | uniq | cut -d : -f 2");
    $cpu_cores = shell_exec("grep -i 'cpu cores' /proc/cpuinfo | uniq | cut -d : -f 2");
    $cpu_threads = shell_exec("grep -ic processor /proc/cpuinfo");
    $cpu_load = 100 - intval(shell_exec("vmstat 1 2|tail -1|awk '{print $15}'"));

    $mem_info = shell_exec("cat /proc/meminfo | head -n 3 | awk '{print $2}'");
    $mem_info_arr = explode("\n", $mem_info);
    $mem_total = round(intval($mem_info_arr[0]) / (1024 ** 2), 2);
    $mem_aval = round(intval($mem_info_arr[2]) / (1024 ** 2), 2);
    $mem_used = $mem_total - $mem_aval;
    try {
        $mem_used_apache_cmd = shell_exec("systemctl status httpd | grep Memory | awk '{print $2}'");
    } catch (Exception $except) {
        $mem_used_apache_cmd = shell_exec("systemctl status apache2 | grep Memory | awk '{print $2}'");
    }
    $mem_used_apache_arr = explode("sh", $mem_used_apache_cmd);
    $mem_used_apache = $mem_used_apache_arr[0];
    $mem_percent = round(($mem_used / $mem_total) * 100, 2);

    $disk_space_total = round(disk_total_space("/") / (1024 ** 3), 2);
    $disk_space_aval = round(disk_free_space("/") / (1024 ** 3), 2);
    $disk_space_used = $disk_space_total - $disk_space_aval;
    $disk_space_used = $disk_space_total - $disk_space_aval;
    $disk_percent = round(($disk_space_used / $disk_space_total) * 100, 2);

    $port_80 = shell_exec("netstat -an | grep ESTABLISHED | awk '{print $4}' | grep \":80\" | wc -l");
    $port_443 = shell_exec("netstat -an | grep ESTABLISHED | awk '{print $4}' | grep \":443\" | wc -l");

    $active_users = intval($port_80) + intval($port_443) - 1;
}
?>

<html>
<head>
    <style>
        table, tr, td {
            padding: 3px;
            text-align: center;
            margin-left: auto;
            margin-right: auto;
        }

        tr, td {
            border: 1px solid black;

        }
    </style>
    <title>System Information</title>

</head>
<body style="text-align:center;padding:50px;">
<h1>SYSTEM INFORMATION</h1>
<table>
    <tr>
        <td>CPU</td>
        <td>
            <table>
                <tr>
                    <td>CPU NAME</td>
                    <td><?php echo $cpu_name ?></td>
                </tr>
                <tr>
                    <td>CPU CORES</td>
                    <td><?php echo $cpu_cores ?></td>
                </tr>
                <tr>
                    <td>CPU THREADS</td>
                    <td><?php echo $cpu_threads ?></td>
                </tr>
                <tr>
                    <td>CPU CURRENT USAGE</td>
                    <td><?php echo $cpu_load ?>%</td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>RAM</td>
        <td>
            <table>
                <tr>
                    <td>TOTAL RAM</td>
                    <td><?php echo $mem_total ?> GB</td>
                </tr>
                <tr>
                    <td>AVALIABLE RAM</td>
                    <td><?php echo $mem_aval ?> GB</td>
                </tr>
                <tr>
                    <td>USED RAM</td>
                    <td><?php echo $mem_used ?> GB</td>
                </tr>
                <tr>
                    <td>USED RAM BY APACHE SERVER</td>
                    <td><?php echo $mem_used_apache ?></td>
                </tr>
                <tr>
                    <td>PERCENTAGE OF RAM USED</td>
                    <td><?php echo $mem_percent ?>%</td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>STORAGE</td>
        <td>
            <table>
                <tr>
                    <td>TOTAL STORAGE</td>
                    <td><?php echo $disk_space_total ?> GB</td>
                </tr>
                <tr>
                    <td>STORAGE AVALIABLE</td>
                    <td><?php echo $disk_space_aval ?> GB</td>
                </tr>
                <tr>
                    <td>STORAGE USED</td>
                    <td><?php echo $disk_space_used ?> GB</td>
                </tr>
                <tr>
                    <td>PERCENTAGE OF DISK USED</td>
                    <td><?php echo $disk_percent ?>%</td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td>USERS</td>
        <td>
            <table>
                <tr>
                    <td>TOTAL USERS LOGGED IN CURRENTLY</td>
                    <td><?php echo $active_users ?> users</td>
                </tr>
            </table>
        </td>
    </tr>

</table>

</body>
</html>


