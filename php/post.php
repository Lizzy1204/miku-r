<?php
    /*
        运行环境:
            linux环境，安装r-base，r可以连接mysql,mysql存在vosmap(数据库名称)数据库
            PHP允许调用linux系统命令
            PHP执行用户有sudo免密码或者root权限
            基础路径（$basepath）拥有读写执行程序的（777）权限
        服务器与MIKU本地版本不同点:
            1.基础路径（$basepath）不同，提交的时候注意
    */
    //垮域名头
    header('Access-Control-Allow-Origin: *');
    //设置文本输出
    //header("Content-Type:text/html;charset=utf-8");
    //设置图片输出头
    header("Content-Type: image/jpeg;text/html; charset=utf-8");
    //基础路径
    //$basepath = "/var/www/html/myweb/miku-r";//本地版
    $basepath = "/var/www/html/miku-r";//服务器版
    //文件名随机值设置
    $tempNum = rand(0,1000);
    //R脚本基础设置
    $rscript_h ="
        ####################################
        setwd(\"$basepath\")
        library(RMySQL)
        #数据库链接,选择vosmap数据库
        conn <- dbConnect(MySQL(), dbname = \"vosmap\", username=\"root\", password=\"MIKU@941004\",host=\"127.0.0.1\",port=3306)
        jpeg(file=\"temp/$tempNum.jpg\")
        ####################################
    ";
    $rscript_f ="
        ####################################
        dev.off()
        dbDisconnect(conn)
        ####################################
    ";
    //接受POST参数
    //R绘图脚本
    $rscript = $_POST["rscript"];
    //是否输出b64代码（默认为true）
    $tobase64 = $_POST["tobase64"];
    if(!isset($tobase64)){
        $tobase64 = "true";
    }
    //拼接R脚本
    $rscript = $rscript_h."".$rscript."".$rscript_f;
    //生成R脚本
    writefile("$basepath/temp/$tempNum.R",$rscript);
    function writefile($fname,$str){
        $fp=fopen($fname,"w");
        fputs($fp,$str);
        fclose($fp);
    }
    //调用linux命令执行R脚本（阻塞）
    shell_exec("sudo Rscript --slave $basepath/temp/$tempNum.R");
    //输出R脚本执行结果图像
    $url = "$basepath/temp/$tempNum.jpg";
    $img = file_get_contents($url,true);
    //输出图像数据
    if($tobase64 == "false"){
        echo $img;
    }else{
        echo base64_encode($img);
    }
    //删除temp文件(temp.R与temp.jpg)
    unlink("$basepath/temp/$tempNum.jpg");
    unlink("$basepath/temp/$tempNum.R");
?>