<?php
   //测试本程序运行环境是否正确
   //1.显示PHP运行用户状态
   echo shell_exec("id -a")."<br/>";
   //2.测试运行权限
   system("sudo mkdir /tmp/test_p",$out);
   echo $out."<br/>" 
?>