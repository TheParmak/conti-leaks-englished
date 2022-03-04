<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
    <title>Web Management System</title>
    <style type="text/css">
        html { height:100%;}
        body { height:100%; text-align:center;}
        .centerDiv {
            display:inline-block;
            zoom:1;
            *display:inline;
            vertical-align:middle;
            text-align:left;
            width:520px;
            padding:0px;
            border:2px solid #0095e1;
        }
        .hiddenDiv {
            height:100%;
            overflow:hidden;
            display:inline-block;
            width:0px;
            margin-left:-1px;
            zoom:1;
            *display:inline;
            *margin-top:-1px;
            margin-top:0;
            vertical-align:middle;
            background-color: #CC6699;}
        .loginTable { border-collapse:collapse;margin-top:0px; border:1px solid #fff; width:520px;}
        .loginTable01 {
            background: url("/images/login.jpg") no-repeat scroll 10px 20px #E8F6FF;
            height: 120px;
            width: 520px;
        }
        .loginTable02 {
            background: url("/images/loginbg.gif") repeat-x scroll 0 0 #4B575E;
            height: 145px;
        }
        .text{font-family:Arial,Helvetica,Geneva,Swiss,SunSans-Regular,sans-serif;font-size:14px;font-weight:bold;}
    </style>
</head>
<body bgColor="#E8F6FF">
    <div class="centerDiv">
        <table align="center" cellpadding="0" cellspacing="0" id="table1" class="loginTable">
            <tr>
                <td colspan="2" class="loginTable01">
                    <br/>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="loginTable02">
                    <?php echo Form::open(); ?>
                        <table align="center" >
                            <tr>
                                <td align="right"  width="30%" class="text">
                                    Username:&nbsp;
                                </td>
                                <td>
                                    <input TYPE="text" id="user" name="username" class="text" style="width:180px">
                                </td>
                            </tr>
                            <tr>
                                <td align="right"  width="30%" class="text">
                                    <font face="Arial"  style="font-weight:bold;color:#000000;">Password:&nbsp;</font>
                                </td>
                                <td>
                                    <input type="password" name="password" onkeydown="if(event.keyCode==13)document.getElementById('login').submit();" style="width:180px">
                                </td>
                            </tr>
                            <tr>
                                <td ALIGN="left"  width=30%></td>
                                <td ALIGN="center">
                                    <input type="submit" name="btn" value="Login">&nbsp;&nbsp;&nbsp;
                                    <input type="reset" onclick="" value="Reset" id="Reset">
                                </td>
                            </tr>
                        </table>
                    <?php echo Form::close(); ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="hiddenDiv"></div>
</body>
</html>