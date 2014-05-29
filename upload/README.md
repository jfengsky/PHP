##HTML5 + PHP 图片上传

[原文参考地址](http://www.36ria.com/4397)

####html表单部分

	<form method="post" enctype="multipart/form-data"  action="upload.php">
		<input type="file" name="images" id="images" multiple />
		<button type="submit" id="btn">Upload Files!</button>
	</form>
	<div id="image-list"></div>
	
* 给form增加<code>enctype=”multipart/form-data”</code>属性，这个属性非常关键，缺少这个属性，将直接提交表单。
* 多选上传的关键是在上传框上加<code>multiple</code>属性。

####php部分

	<?php
 		foreach ($_FILES["images"]["error"] as $key => $error) {
 			if ($error == UPLOAD_ERR_OK) {
 				$name = $_FILES["images"]["name"][$key];
 				move_uploaded_file( $_FILES["images"]["tmp_name"][$key], "uploads/" . $_FILES['images']['name'][$key]);
 			}
 		}
 	echo "<h2>文件成功上传！</h2>";
 	?>
 	
非常简化的php脚本，没做任何判断和限制

####重点的js部分
监听上传框的change事件,然后利用<code>FileReader</code>显示要上传的图片。[FileReader的W3C文档](http://www.w3.org/TR/FileAPI/)

	var input = document.getElementById("images"),
    	list = document.getElementById('image-list');
	input.addEventListener("change", function (evt) {
	  var files = this.files;
	    for ( var i = 0, len = files.length; i < len; i++ ) {
	      var file = this.files[i];
	      //文件类型为图片
	      if (!!file.type.match(/image.*/)) {
	        //浏览器支持FileReader对象
	        if ( window.FileReader ) {
	          var reader = new FileReader();
	          //监听文件读取结束后事件
	          reader.onloadend = function (e) {
	            var img = new Image();
	            img.src = e.target.result;
	            list.appendChild(img);
	          };
	          reader.readAsDataURL(file);
	        }
	      }
	    }
	});

FileReader有6个事件

* onloadstart
* onprogress
* onload
* onabort
* onerror
* onloadend 监听读取完毕事件

留意e.target.result，result包含图片base64数据

ajax发送FormData数据

	//发送ajax请求，存储文件（传递FormData对象过去）
	if (formdata) {
	    $.ajax({
	        url: "upload.php",
	        type: "POST",
	        data: formdata,
	        processData: false,
	        contentType: false,
	        success: function (res) {
	            //将上传成功后的提示打印到页面
	            document.getElementById("response").innerHTML = res;
	        }
	    });
	}
	
全部js代码如下:

	(function () {
	  var input = document.getElementById("images"), 
	    formdata = false;
	    //显示上传图片
	  function showUploadedItem (source) {
	      var list = document.getElementById("image-list"),
	        li   = document.createElement("li"),
	        img  = document.createElement("img");
	      img.src = source;
	      li.appendChild(img);
	    list.appendChild(li);
	  }   
	    //如果浏览器不支持FormData，隐藏按钮
	  if (window.FormData) {
	      formdata = new FormData();
	      document.getElementById("btn").style.display = "none";
	  }
	  //监听上传框的change事件
	  input.addEventListener("change", function (evt) {
	        //改变消息层的文案
	    document.getElementById("response").innerHTML = "Uploading . . ."
	    var i = 0, len = this.files.length, img, reader, file;
	      //遍历文件
	    for ( ; i < len; i++ ) {
	      file = this.files[i];
	          //文件类型为图片
	      if (!!file.type.match(/image.*/)) {
	                //浏览器支持FileReader对象
	        if ( window.FileReader ) {
	          reader = new FileReader();
	                    //监听文件读取结束后事件
	          reader.onloadend = function (e) {
	                        //将图片添加到显示列表
	            showUploadedItem(e.target.result, file.fileName);
	          };
	                    //读取文件
	          reader.readAsDataURL(file);
	        }
	                //将文件数据添加到FormData对象内
	        if (formdata) {
	          formdata.append("images[]", file);
	        }
	      }
	    }
	//发送ajax请求，存储文件（传递FormData对象过去）
	if (formdata) {
	    $.ajax({
	        url: "upload.php",
	        type: "POST",
	        data: formdata,
	        processData: false,
	        contentType: false,
	        success: function (res) {
	            //将上传成功后的提示打印到页面
	            document.getElementById("response").innerHTML = res;
	        }
	    });
	}
	  }, false);
	}());


http://www.leeon.me/upload/other/swfupload.html