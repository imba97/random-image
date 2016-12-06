<?php
if(!empty($_POST['animate']))
{
  header('Content-type: application/json');
  $defaultFileName='randBG.php';     //生成的文件名
  $defaultImgPath='./boom';          //图片路径（根据这个路径会自动把所有以下设置可用格式的图片读取出来）
  $status=NULL;                  //状态
  $returnMsg='';
  $imgPath=empty($_POST['url'])?$defaultImgPath:$_POST['url'];     //如果没有自定义路径则按照以上默认路径
  $fileName=empty($_POST['name'])?$defaultFileName:$_POST['name']; //如果没有自定义文件名则按照以上默认文件名
  $imgPath=str_replace('\\','/',$imgPath);
  $pathLen=strlen($imgPath);
  $imgPath=$imgPath[$pathLen-1]=='/'?$imgPath:$imgPath.'/';

  $fileName1=explode('.',$fileName);
  if(is_array($fileName1)&&end($fileName1)!='php') $fileName.='.php';
  if(empty($fileName1[0]))
  {
    $fileName=$defaultFileName;
    $returnMsg=$returnMsg!=''?$returnMsg:'文件名已改为默认文件名';
    $status=$status!=NULL?$status:2;
  }

  $imgName=array();
  $reText='';

  $imgDir=@scandir($imgPath);
  if(!$imgDir)
  {
    $returnMsg=$returnMsg!=''?$returnMsg:'没有此文件夹';
    $status=$status!=NULL?$status:0;
  }
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

  if(count($imgName)==0)
  {
    $returnMsg=$returnMsg!=''?$returnMsg:'文件夹中没有图片';
    $status=$status!=NULL?$status:0;
  }
  else
  {
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
    $file=@fopen($fileName,"w+");
    if(!$file)
    {
      $returnMsg=$returnMsg!=''?$returnMsg:'文件创建失败';
      $status=$status!=NULL?$status:0;
      $returnInfo=array(
        'status'    =>  $status,
        'returnMsg' =>  $returnMsg,
        'animate'   =>  $_POST['animate']
      );
      print_r(json_encode($returnInfo));
      die();
    }
    fwrite($file,$reText);
    fclose($file);
    $returnMsg=$returnMsg!=''?$returnMsg:'貌似成功了，一共 '.intval(count($imgName)).' 张图';
    $status=$status!=NULL?$status:1;
  }
  $returnInfo=array(
    'status'    =>  $status,
    'returnMsg' =>  $returnMsg,
    'animate'   =>  $_POST['animate']
  );
  print_r(json_encode($returnInfo));
  die();
}
?>


<!DOCTYPE html>
<head>
<title>只是一个普通简单并没什么卵用的代码生成工具</title>
<link rel="stylesheet" type="text/css" href="./style.css" />
</head>
<body>
  <div class="abc"></div>
  <from action="#" method="post">
    <div class="top-div">
      <p id="returnMsg"></p>
      <a class="top-a" id="data-upload" href='javascript:;'>屠龙宝刀点击就Update</a>
      <a class="btn-data" href="javascript:;">设置</a>
      <div class="data-div">
        <div class="data-div-l">
          <p>指定路径：</p>
          <p>自定义文件名：</p>
        </div>
        <div class="data-div-r">
          <p><input type="text" id="getUrl" /></p>
          <p><input type="text" id="getName" /></p>
        </div>
        <div class="data-div-b"></div>
        <a class="data-div-confirm" href="javascript:;">确定</a>
      </div>
    </div>
  </from>
</body>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script type="text/javascript" language="javascript">
var checkAnimate=true;
$(document).ready(function(){
  $(".btn-data,.data-div-confirm").click(function(){
    $(this).blur();
    if(!checkAnimate) return;
    checkAnimate=false;
    if($(".data-div").is(':hidden'))
    {
      $(".data-div").css({'display':'block','opacity':0}).animate({'left':0,'opacity':1},200,function(){
        checkAnimate=true;
        $(".btn-data").html('取消');
      });
    }
    else
    {
      $(".data-div").animate({'left':-100,'opacity':0},200,function(){
        checkAnimate=true;
        $(this).css({'display':'none'});
        $(".btn-data").html('设置');
      });

      //确认按钮被点击
      var btn=$(this).attr('class');
      if(btn=='data-div-confirm')
      {
        var url=$("#getUrl").val();
        var name=$("#getName").val();
        var data={
          'url':url,
          'name':name,
          'animate':true
        };
        action(data);
      }
    }
  });

  $("#data-upload").click(function(){
    action({'url':'./taidada','name':'randBG.php','animate':false});
  });

  //键盘事件，回车倍按下后等于点击确认按钮
  $(document).keypress(function(e){
    if(e.keyCode==13)
    {
      if(!$(".data-div").is(":hidden"))
      {
        $(".data-div-confirm").click();
      }
    }
  });
});

function action(data,animate=false)
{
  $.ajax({
    url:'./index.php',
    async:false,
    data:data,
    type:'POST',
    dataType:'json',
    success:function(json){
      $("#returnMsg").html(json.returnMsg);
      switch(json.status)
      {
        case 0:
          $("#returnMsg").css('background-color','#852e2e');
        break;
        case 1:
          $("#returnMsg").css('background-color','#4c852e');
        break;
        case 2:
          $("#returnMsg").css('background-color','#969117');
        break;
      }
      if(json.animate)
      {
        setTimeout(function(){
          checkAnimate=false;
          $("#data-upload").animate({'left':-100,'opacity':0},200,function(){
            $(this).css({'display':'none'});
            checkAnimate=true;
          });
        },200);
      }
      else
      {
        checkAnimate=false;
        $("#data-upload").animate({'left':-100,'opacity':0},200,function(){
          $(this).css({'display':'none'});
          checkAnimate=true;
        });
      }
    }
  });
}
</script>
</html>
