<?php
if(!empty($_GET['update'])&&$_GET['update']==1)
{
  $fileName='randBG.php';     //生成的文件名
  $imgPath='./boom';          //图片路径（根据这个路径会自动把所有以下设置可用格式的图片读取出来）
  // $imgPath=empty($_GET['url'])?'./':$_GET['url'];
  $imgPath=str_replace('\\','/',$imgPath);
  $pathLen=strlen($imgPath);
  $imgPath=$imgPath[$pathLen-1]=='/'?$imgPath:$imgPath.'/';

  $imgName=array();
  $reText='';

  $imgDir=scandir($imgPath);
  $imgNum=count($imgDir);
  for($i=0;$i<$imgNum;$i++)
  {
    if(basename($imgDir[$i])!='.'&&basename($imgDir[$i])!='..') //过滤两个特殊文件夹
    {
      $iName=basename($imgDir[$i]);
      $iName1=explode('.',basename($imgDir[$i]));
      $iName2=end($iName1);
      if($iName2=='jpg'||$iName2=='jepg'||$iName2=='png'||$iName2=='gif') //设置可用格式
      {
        if($imgPath=='./') $imgName[]=$iName; else $imgName[]=$imgPath.$iName;
      }
    }
  }

  $reText.='<?php $images=array(';
  foreach($imgName as $v)
  {
    $reText.='\''.$v.'\'';
    if($v!=$imgName[count($imgName)-1])
    {
      $reText.=',';
    }
  }
  $reText.=');'.'$rand=rand(0,count($images)-1);header("Content-type: image/jpeg");readfile($images[$rand]);?>';

  $file=fopen($fileName,"w+");
  fwrite($file,$reText);
  fclose($file);
  $msg='貌似成功了，一共 '.intval(count($imgName)).' 张图';
  echo $msg;
}
else
{
?>

<!DOCTYPE html>
<head>
<title>只是一个普通简单并没什么卵用的代码生成工具</title>
</head>
<body>
  <div style="position:relative;margin:100px auto;width:600px;height:200px;background-color:#35a0cf;border-radius:35px;-webkit-box-shadow:3px 3px 3px #CCC;-moz-box-shadow:3px 3px 3px #CCC;box-shadow:3px 3px 3px #CCC;">
    <a style="position:absolute;top:0;left:0;width:600px;height:200px;line-height:200px;color:#FFF;font-size:40px;font-family:'微软雅黑';text-align:center;text-decoration:none" href='?update=1'>屠龙宝刀点击就Update</a>
  </div>
</body>
</html>

<?php
}
?>
