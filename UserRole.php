
<?php
require ('connector.php');
require ('validateSession.php');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>user-role</title>

        <!-- Google Fonts -->
        <link href='https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700|Lato:400,100,300,700,900' rel='stylesheet' type='text/css'>

        <link rel="stylesheet" href="animate.css">
        <!-- Custom Stylesheet -->
        <link rel="stylesheet" href="style1.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script src="jquery-3.2.1.min.js" type="text/javascript"></script>

        <script>
            window.flag = false;
            $(document).ready(function () {
                var user = $("#user");
                var userLoadSettings = {
                    type: "POST",
                    datatype: "json",
                    url: "SM_API.php",
                    data: {"act": "loadUsers"},
                    success: function (r) {
                        user.append(r);
                    },
                    error: function () {
                        alert("some error occured");
                    }
                };
                $.ajax(userLoadSettings);

                var role = $("#role");
                var roleLoadSettings = {
                    type: "POST",
                    datatype: "json",
                    url: "SM_API.php",
                    data: {"act": "loadRoles"},
                    success: function (r) {
                        role.append(r);
                    },
                    error: function () {
                        alert("some error occured");
                    }
                };
                $.ajax(roleLoadSettings);


                var table = $("#data");
                var tr;
                var td;
                var $delBtn;
                var $editBtn;
                var editID;
                var loadTableSettings = {
                    type: "POST",
                    datatype: "json",
                    url: "SM_API.php",
                    data: {"act": "loadUserRoleTable"},
                    success: function (r) {
                        r = $.parseJSON(r);
                        //alert(r);
                        for (var i = 0; i < r.length; i++) {
                            tr = $("<tr>");
                            for (var k in r[i]) {
                                if (k != "id") {
                                    td = $("<td>").text(r[i][k]);
                                    tr.append(td);
                                } else {
                                    editID = r[i][k];
                                }
                            }
                            $editBtn = $("<button>").text("Edit");
                            $editBtn.attr("id", "edit");
                            $editBtn.attr("value", editID);
                            $editBtn.attr("type", "submit");

                            $editBtn.bind("click", function () {
                                flag = true;
                                $("#role").html("<option value='0'>--Select--</option>");
                                $("#user").html("<option value='0'>--Select--</option>");
                                var id = $(this).attr("value");
                                var editSetting = {
                                    type: "POST",
                                    datatype: "json",
                                    url: "SM_API.php",
                                    data: {"id": id, "act": "editUserRole"},
                                    success: function (response) {
                                        var r = $.parseJSON(response);
                                        var role = r["role"];
                                        var user = r["user"];
                                        var selectedRoleSettings = {
                                            type: "POST",
                                            datatype: "json",
                                            url: "SM_API.php",
                                            data: {"id": role, "act": "loadRoles"},
                                            success: function (res) {
                                                $("#role").append(res);
                                            },
                                            error: function () {
                                                alert("Some problem occured");
                                            }
                                        };
                                        $.ajax(selectedRoleSettings);

                                        var selectedUserSettings = {
                                            type: "POST",
                                            datatype: "json",
                                            url: "SM_API.php",
                                            data: {"id": user, "act": "loadUsers"},
                                            success: function (res) {
                                                $("#user").append(res);
                                            },
                                            error: function () {
                                                alert("Some problem occured");
                                            }
                                        };
                                        $.ajax(selectedUserSettings);

                                        var $sbtn = $("#s");
                                        if (flag == true) {
                                            $sbtn.bind("click", function () {

                                                var e_role = $("#role").val();
                                                var e_user = $("#user").val();
                                                //alert(e_role);
                                                //alert(e_user);
                                                var updateSetting = {
                                                    type: "POST",
                                                    datatype: "json",
                                                    url: "SM_API.php",
                                                    data: {"id": id, "role": e_role, "user": e_user, "update": "update", "act": "SaveUserRole"},
                                                    success: function (r) {
                                                        r = $.parseJSON(r);
                                                        if (r["updates"] == "updated") {
                                                            alert("Successfully updated.");
                                                            window.location.href="UserRole.php";
                                                        }
                                                    },
                                                    error: function () {
                                                        alert("Some error occured");
                                                    }
                                                };
                                                $.ajax(updateSetting);
                                            });
                                        }
                                    },
                                    error: function () {
                                        alert("Some problem has occured");
                                    }
                                };
                                $.ajax(editSetting);

                            });

                            td = $("<td>").append($editBtn);
                            tr.append(td);

                            $delBtn = $("<button>").text("Delete");
                            $delBtn.attr("id", "edit");
                            $delBtn.attr("value", editID);
                            $delBtn.attr("type", "submit");

                            $delBtn.bind("click", function () {
                                var $isConfirm = confirm("Record will be deleted. Click Ok to continue and Cancel to Ignore");
                                if ($isConfirm == true) {
                                    $(this).closest("tr").remove();
                                    var delId = $delBtn.attr("value");
                                    var delSettings = {
                                        type: "POST",
                                        datatype: "json",
                                        url: "SM_API.php",
                                        data: {"id": delId, "act": "DeleteUserRole"},
                                        success: function (r) {
                                            if ($.parseJSON(r) == "User-Role deleted successfully") {
                                                alert("User-Role deleted successfully")
                                            }
                                        },
                                        error: function () {
                                            alert("Some problem occured");
                                        }
                                    };
                                    $.ajax(delSettings);
                                } else
                                    return false;
                            });

                            td = $("<td>").append($delBtn);
                            tr.append(td);

                            table.append(tr);
                        }

                    },
                    error: function () {
                        alert("some error occured");
                    }
                };
                $.ajax(loadTableSettings);

                $("#s").click(function () {
                    if (flag == false) {
                        var role = $("#role").val();
                        var user = $("#user").val();
                        console.log(role);
                        console.log(user);
                        if (role == 0 || user == 0) {
                            alert("Some fields are empty");
                            return;
                        }
                        var saveSettings = {
                            type: "POST",
                            datatype: "json",
                            url: "SM_API.php",
                            data: {"role": role, "user": user, "act": "SaveUserRole"},
                            success: function (r) {
                                r = $.parseJSON(r);
                                if (r["added"] == "Successfully Added") {
                                    alert("Successfully Added");
                                    window.location.href="UserRole.php";
                                }
                            },
                            error: function () {
                                alert("Some problem occured");
                            }
                        };
                        $.ajax(saveSettings);
                    }
                });

            });

        </script>

        <style>

            .optStyle{
                width:190px ;
                margin-bottom: 20px;
                padding: 8px;
                border: 1px solid #ccc;
                border-radius: 2px;
                font-size: .9em;
                color: #888;
            }



            body{
                margin: 0px;
                background-image: url("photo_bg.jpg");
                height: auto;
                background-position: center;
                background-repeat: no-repeat;
                background-size: cover;
            }

        </style>



    </head>

    <body >

        <?php include 'Header.php'; ?>

        <div class="container" id="div1" >
            <table cellspacing="4" cellpadding="60px" >
                <tr>
                    <td style="width:400px">
                        <div id="div2" class="login-box">
                            <div class="box-header">
                                <h2>User-Role Management</h2>
                            </div>
                            <label for="user">User</label>
                            <br/>
                            <select id="user" class="optStyle">
                                <option value="0">--Select-- </option>

                            </select><div style="color:saddlebrown"> </div>
                            <br/>
                            <label for="role">Role</label>
                            <br/>
                            <select  id="role" class="optStyle">
                                <option value="0">--Select--</option>

                            </select><div style="color:saddlebrown"> </div>
                            <br/>
                            <button type="submit" name="save" id="s">Save</button>
                            <br/>
                            <div style="color:saddlebrown"> </div>
                        </div>
                    </td>

                    <td style="width:auto">
                        <div id="div2" class="login-box animated fadeInUp">
                            <div class="box-header">
                                <h2>User-Role</h2>
                            </div>

                            <table id="data" style="border:1px; text-align: left;" cellspacing="4" cellpadding="4" >
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                </tr>
                            </table>
                        </div>
                    </td>

                </tr>
            </table>
        </div>

    </body>

    <script>
        $(document).ready(function () {
            $('#logo').addClass('animated fadeInDown');
            $("input:text:visible:first").focus();
        });
        $('#username').focus(function () {
            $('label[for="username"]').addClass('selected');
        });
        $('#username').blur(function () {
            $('label[for="username"]').removeClass('selected');
        });
        $('#password').focus(function () {
            $('label[for="password"]').addClass('selected');
        });
        $('#password').blur(function () {
            $('label[for="password"]').removeClass('selected');
        });
    </script>

</html>