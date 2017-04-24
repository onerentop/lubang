<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登录</title>
    <script src="//cdn.bootcss.com/jquery/3.1.1/jquery.min.js"></script>
</head>
<body>
<div>
    <form action="#" method="post">
        <lable>电话</lable>
        <input type="text" name="tel" id="tel">
        <lable>验证码</lable>
        <input type="text" name="code" id="code">
        <input type="button" value="发送验证码" id="getCode">
        <input type="button" value="提交" onclick="validation()">
    </form>
</div>
<script type="text/javascript">
    var code = $('#getCode')[0];
    code.onclick = (function () {
        var tel = $('#tel')[0].value;
        $.post("http://oneren.top/lubang/Home/code", {"tel": tel},
            function (data) {
            }, "json");
    });

    function validation() {
        var code = $('#code')[0].value;
        var tel = $('#tel')[0].value;
        console.log(code);
        $.post("http://oneren.top/lubang/Home/validation", {"code": code, "tel": tel},
            function (data) {
                if (data.status == 1) {
                    alert('成功');
                } else {
                    alert('失败');
                }
            }, "json");
    }
</script>
</body>
</html>