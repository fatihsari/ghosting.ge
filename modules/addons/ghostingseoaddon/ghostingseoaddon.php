<?php

if (!defined("WHMCS"))
    die("This file cannot be accessed directly");

function ghostingseoaddon_config() {
    $configarray = array(
        "name" => "gHosting SEO Addon",
        "description" => "gHosting SEO addon module. Manage Page Title, Description & Keywords.",
        "version" => "1.0",
        "author" => "ghosting.ge",
        "language" => "english",
    );
    return $configarray;
}

function ghostingseoaddon_upgrade($vars) {
    //Upgrade
}

function ghostingseoaddon_activate() {
    $query = "CREATE TABLE `mod_ghostingseoaddon` (`id` INT( 1 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , `regex` TEXT NOT NULL, `pageheader_georgian` TEXT NOT NULL, `pageheader_english` TEXT NOT NULL, `pageheader_russian` TEXT NOT NULL, `keyword_georgian` TEXT NOT NULL, `keyword_english` TEXT NOT NULL, `keyword_russian` TEXT NOT NULL, `description_georgian` TEXT NOT NULL, `description_english` TEXT NOT NULL, `description_russian` TEXT NOT NULL, `ogurl` TEXT NOT NULL, `ogtype` TEXT NOT NULL, `ogtitle_georgian` TEXT NOT NULL, `ogtitle_english` TEXT NOT NULL, `ogtitle_russian` TEXT NOT NULL, `ogimage` TEXT NOT NULL, `ogdesc_georgian` TEXT NOT NULL, `ogdesc_english` TEXT NOT NULL, `ogdesc_russian` TEXT NOT NULL)";	
    $result = full_query($query);
    return array('status' => 'success', 'description' => 'gHosting SEO Addon activated successfully');
}

function ghostingseoaddon_deactivate() {
    $query = "DROP TABLE `mod_ghostingseoaddon`";
    $result = full_query($query);
    return array('status' => 'success', 'description' => 'gHosting SEO Addon deactivated successfully');
}

function ghostingseoaddon_output($vars) {
    if (isset($_REQUEST['deleteseo'])) {
        $id = $_REQUEST['deleteseo'];
        $query = "Delete from mod_ghostingseoaddon where id='$id';";
        mysql_query($query);
        echo "<div class='alert alert-info'>Deleted Successfull</div>";
    } else if (isset($_REQUEST['editseo'])) {
        $id = $_REQUEST['editseo'];
        $sql = "SELECT * FROM mod_ghostingseoaddon where id='$id'";
        $result = mysql_query($sql);
        while ($data = mysql_fetch_array($result)) {
            $id = $data['id'];
            $regex = $data['regex'];
            $keyword_georgian = $data['keyword_georgian'];
            $keyword_english = $data['keyword_english'];
            $keyword_russian = $data['keyword_russian'];
            $description_georgian = $data['description_georgian'];
            $description_english = $data['description_english'];
            $description_russian = $data['description_russian'];
            $pageheader_georgian = $data['pageheader_georgian'];
            $pageheader_english = $data['pageheader_english'];
            $pageheader_russian = $data['pageheader_russian'];
            $ogurl = $data['ogurl'];
            $ogtype = $data['ogtype'];
            $ogtitle_georgian = $data['ogtitle_georgian'];
            $ogtitle_english = $data['ogtitle_english'];
            $ogtitle_russian = $data['ogtitle_russian'];
            $ogimage = $data['ogimage'];
            $ogdesc_georgian = $data['ogdesc_georgian'];
            $ogdesc_english = $data['ogdesc_english'];
            $ogdesc_russian = $data['ogdesc_russian'];
        }
    }
    if (isset($_POST['regex'])) {
        $regex = $_POST['regex'];
        if (!empty($regex) && $regex != "") {
            $keyword_georgian = stripslashes($_POST['keyword_georgian']);
            $keyword_english = stripslashes($_POST['keyword_english']);
            $keyword_russian = stripslashes($_POST['keyword_russian']);
            $description_georgian = stripslashes($_POST['description_georgian']);
            $description_english = stripslashes($_POST['description_english']);
            $description_russian = stripslashes($_POST['description_russian']);
            $pageheader_georgian = stripslashes($_POST['pageheader_georgian']);
            $pageheader_english = stripslashes($_POST['pageheader_english']);
            $pageheader_russian = stripslashes($_POST['pageheader_russian']);
            $ogurl = stripslashes($_POST['ogurl']);
            $ogtype = stripslashes($_POST['ogtype']);
            $ogtitle_georgian = stripslashes($_POST['ogtitle_georgian']);
            $ogtitle_english = stripslashes($_POST['ogtitle_english']);
            $ogtitle_russian = stripslashes($_POST['ogtitle_russian']);
            $ogimage = stripslashes($_POST['ogimage']);
            $ogdesc_georgian = stripslashes($_POST['ogdesc_georgian']);
            $ogdesc_english = stripslashes($_POST['ogdesc_english']);
            $ogdesc_russian = stripslashes($_POST['ogdesc_russian']);
            $id = $_POST['id'];
            if ($id == "") {
                $query = "INSERT INTO mod_ghostingseoaddon (`regex`, `pageheader_georgian`, `pageheader_english`, `pageheader_russian`, `keyword_georgian`, `keyword_english`, `keyword_russian`,`description_georgian`,`description_english`,`description_russian`, `ogurl`, `ogtype`, `ogtitle_georgian`, `ogtitle_english`, `ogtitle_russian`, `ogimage`, `ogdesc_georgian`, `ogdesc_english`, `ogdesc_russian`) VALUES ('$regex', '$pageheader_georgian', '$pageheader_english', '$pageheader_russian','$keyword_georgian','$keyword_english','$keyword_russian', '$description_georgian', '$description_english', '$description_russian', '$ogurl', '$ogtype', '$ogtitle_georgian', '$ogtitle_english', '$ogtitle_russian', '$ogimage', '$ogdesc_georgian', '$ogdesc_english', '$ogdesc_russian' )";
                echo "<div class='alert alert-success'>Page SEO Inserted</div>";
            } else {
                $query = "update mod_ghostingseoaddon set `regex`='$regex', `pageheader_georgian`='$pageheader_georgian', `pageheader_english`='$pageheader_english', `pageheader_russian`='$pageheader_russian', `keyword_georgian`='$keyword_georgian', `keyword_english`='$keyword_english', `keyword_russian`='$keyword_russian', `description_georgian`='$description_georgian', `description_english`='$description_english', `description_russian`='$description_russian', `ogurl`='$ogurl', `ogtype`='$ogtype', `ogtitle_georgian`='$ogtitle_georgian', `ogtitle_english`='$ogtitle_english', `ogtitle_russian`='$ogtitle_russian', `ogimage`='$ogimage', `ogdesc_georgian`='$ogdesc_georgian', `ogdesc_english`='$ogdesc_english', `ogdesc_russian`='$ogdesc_russian' where id='$id'";
                echo "<div class='alert alert-success'>Page SEO updated</div>";
            }
            mysql_query($query);
        } else if (isset($regex)) {
            echo "<div class='alert alert-danger'>Please enter Page URL and Other Details</div>";
        }
        $regex = "";
        $pageheader_georgian = "";
        $pageheader_english = "";
        $pageheader_russian = "";
        $keyword_georgian = "";
        $keyword_english  = "";
        $keyword_russian  = "";
        $description_georgian = "";
        $description_english  = "";
        $description_russian  = "";
        $id = "";
        $ogurl = "";
        $ogtype = "";
        $ogtitle_georgian = "";
        $ogtitle_english  = "";
        $ogtitle_russian  = "";
        $ogimage = "";
        $ogdesc_georgian = "";
        $ogdesc_english  = "";
        $ogdesc_russian  = "";
    }
    echo '
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/css/dataTables.bootstrap.min.css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.12/js/dataTables.bootstrap.min.js"></script>

<ul class="nav nav-tabs" id="myTab">
   <li class="active"><a data-toggle="tab" href="#home">Create New</a></li>
   <li><a data-toggle="tab" href="#menu1">SEO Records</a></li>
</ul>
<div class="tab-content" style="padding-top:10px;">
    <div id="home" class="tab-pane fade in active"> 
        <form class="form-horizontal " action="" method="post" id="JqSeoForm">
            <input type="hidden" name="action" value="save" />
              <input type="hidden" name="id" value="' . $id . '">
                    <div class="col-lg-5 ccc">
                        <div class="form-group fg1">
                            <label class="col-lg-3 control-label cont-label" for="inputMode">URL (Regex)</label>
                                <div class="col-lg-9 ">
                                    <input type="text" class="form-control form-cl1" placeholder="Enter the WHMCS Page URL" name="regex" value="' . str_replace('\\', '\\\\', $regex) . '">
                                      </div>
                                </div>
                        <div class="form-group fg2">
                            <label class="col-lg-3 control-label cont-label" for="inputMode">Meta Title</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="Georgian" name="pageheader_georgian"  value="' . $pageheader_georgian . '">
                                    <input type="text" class="form-control" placeholder="English" name="pageheader_english"  value="' . $pageheader_english . '">
                                    <input type="text" class="form-control" placeholder="Russian" name="pageheader_russian"  value="' . $pageheader_russian . '">
                                       </div>
                                </div>  
                        <div class="form-group fg3">
                            <label class="col-lg-3 control-label cont-label" for="inputMode">Meta Keyword</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" name="keyword_georgian" placeholder="Georgian" value="' . $keyword_georgian . '" >
                                    <input type="text" class="form-control" name="keyword_english" placeholder="English" value="' . $keyword_english . '" >
                                    <input type="text" class="form-control" name="keyword_russian" placeholder="Russian" value="' . $keyword_russian . '" >
                               </div>
                         </div>
                        <div class="form-group fg4">
                            <label class="col-lg-3 control-label cont-label" for="inputMode">Meta Description</label>
                                <div class="col-lg-9">
                                    <textarea cols="15"  rows="3" class="form-control" placeholder="Georgian" name="description_georgian">' . $description_georgian . '</textarea>
                                    <textarea cols="15"  rows="3" class="form-control" placeholder="English" name="description_english">' . $description_english . '</textarea>
                                    <textarea cols="15"  rows="3" class="form-control" placeholder="Russian" name="description_russian">' . $description_russian . '</textarea>
                               </div>
                         </div>
                   </div>
                    <div class="col-lg-6 divcl">
					    <p class="text-center wall wall-sm">
							for more infomation about OpenGraph Sharing/Facebook sharing, please refer <a href="https://developers.facebook.com/docs/sharing/webmasters">here</a>
						</p>
                        <div class="form-group ">
                            <label class="col-lg-3 control-label cont-label" for="inputMode">OG URL</label>
                                <div class="col-lg-9">
                                    <input type="text" class="form-control" placeholder="Enter the Facebook Share URL" name="ogurl" value="' . $ogurl . '">
                              </div>
                         </div>
                    <div class="form-group fg5">
                        <label class="col-lg-3 control-label cont-label" for="inputMode">OG:Type</label>
                            <div class="col-lg-9">
                               <input type="text" class="form-control" placeholder="Enter the Facebook Share Type" name="ogtype"  value="' . $ogtype . '"> 
                                  </div>
                            </div>
                    <div class="form-group fg6">
                        <label class="col-lg-3 control-label cont-label" for="inputMode">OG:Title</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" placeholder="Georgian" name="ogtitle_georgian" value="' . $ogtitle_georgian . '"> 
                                <input type="text" class="form-control" placeholder="English" name="ogtitle_english" value="' . $ogtitle_english . '"> 
                                <input type="text" class="form-control" placeholder="Russian" name="ogtitle_russian" value="' . $ogtitle_russian . '"> 
                                    </div>
                              </div>
                    <div class="form-group fg7">
                        <label class="col-lg-3 control-label cont-label" for="inputMode">OG:Image</label>
                            <div class="col-lg-9">
                                <textarea cols="15" rows="3" class="form-control" placeholder="Enter the Facebook Share Image" name="ogimage">' . $ogimage . '</textarea>
                           </div>
                      </div>
                    <div class="form-group">
                        <label class="col-lg-3 control-label cont-label" for="inputMode">OG:Description</label>
                            <div class="col-lg-9">
                                <textarea cols="15" rows="2" class="form-control" placeholder="Georgian" name="ogdesc_georgian">' . $ogdesc_georgian . '</textarea>
                                <textarea cols="15" rows="2" class="form-control" placeholder="English" name="ogdesc_english">' . $ogdesc_english . '</textarea>
                                <textarea cols="15" rows="2" class="form-control" placeholder="Russian" name="ogdesc_russian">' . $ogdesc_russian . '</textarea>
                           </div>
                     </div>
              </div> 
                    <div class="col-lg-10 klm">
                         <p align="center"><input type="submit" id="seosave" name="seosave" value="Save" class="btn btn-submit"/></p>                                
</div>     
      </form>     
            </div>
                    <div id="menu1" class="tab-pane fade">
                    <div class="col-lg-12">
                    <table width="100%" id="example-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="tblhd1">No</th>
                                <th class="tblhd2">Page URL</th>
                                    <th class="tblhd3">Title (Georgian)</th>
                                    <th class="tblhd3">Title (English)</th>
                                    <th class="tblhd3">Title (Russian)</th>
                                        <th class="tblhd4">Action</th>
                                            </tr>
                                                </thead>
                                                    <tbody>
';
    /* Getting messages order by date desc */
    $sql = "SELECT regex,pageheader_georgian, pageheader_english, pageheader_russian,id FROM mod_ghostingseoaddon order by id";
    $result = mysql_query($sql);
    while ($data = mysql_fetch_array($result)) {
        $sam[] = $data;
    }
    foreach ($sam as $key => $val) {
        $p = $key + 1;
        echo '<tr class="tblrow">';
        echo '<td class="tblcol" style="width:5%">' . $p . '</td>';
        echo '<td>' . $val['regex'] . '</td>';
        echo '<td>' . $val['pageheader_georgian'] . '</td>';
        echo '<td>' . $val['pageheader_english'] . '</td>';
        echo '<td>' . $val['pageheader_russian'] . '</td>';
        echo '<td style="width:10%"><a href="addonmodules.php?module=ghostingseoaddon&editseo=' . $val['id'] . '" title="edit">'
        . '<span class="glyphicon glyphicon-edit text-info"></a>'
        . '&nbsp;<a href="addonmodules.php?module=ghostingseoaddon&deleteseo=' . $val['id'] . '" title="delete">'
        . '<span class="glyphicon glyphicon-trash text-danger"></a> </td></tr>';
    }
    echo '</tbody> </table>';
    echo '</div></div></div>';
    echo '<script type="text/javascript">
    $(document).ready(function()
    {
        $("#example-table").dataTable();
        $("#example-table_wrapper .row:last-child").children("div").removeClass("col-sm-6").addClass("col-sm-4");       
})
</script>';
    echo '<script type="text/javascript">
$(document).ready(function()
{
$("#errorbox").delay(1000).fadeOut();
$("#successbox").delay(1000).fadeOut();
$("#updatebox").delay(1000).fadeOut();
});
</script>';
}