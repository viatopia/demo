<?php
include("header.php");

$id = $_REQUEST['id'];
$action = $_POST['action'];

if ($_POST) {
    switch($action) {
        case "Add" :
            $status = $database->insert("users", $_POST);
            $id="";
            break;
        case "Update" :
            $status = $database->update("users", $_POST, "id=:id");
            $id="";
            break;
        case "Delete" :
            $status =  $database->delete("users", "id=$id");
            $id="";
            break;
    }
}

if ($id > 0) {
    $sql="SELECT * FROM users WHERE id=:id LIMIT 1";
    $parameters = Array("id"=>$id);
    $user = $database->select($sql,$parameters);    
    //if (count($rs) > 0) {
        //$device=$rs[0]["device"];
        //$active=$rs[0]["active"];
    //} else {
        //$id="";
    //}
}

if ($id > 0) { $action = "Update"; } else { $action = "Add"; }
?>
<div id="content">
    <?php if (strlen($status) > 0) { ?><div id="status"><?php echo $status; ?></div><?php } ?>
    <div id="title">Setup User Accounts</div>    
    <div style="float:left; width: 300px; height: 100%; position: relative;">
        <div class="sub-title"><?php echo $action; ?> User Account</div>
        <form name="form" action="<?php echo $_SERVER['PHPSELF']; ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <input type="hidden" name="active" value="0">
            <input type="hidden" name="admin" value="0">
            <input type="hidden" name="full" value="0">
            <input type="hidden" name="read" value="0">
            <input type="hidden" name="manage" value="0">
            <div class="row">
               <label>Active</label>
                <div class="data"><input type="checkbox" name="active" value="1" <?php if ($user[0]['active']==1) { echo "CHECKED"; } ?>/></div>
            </div>             
            <div class="row">
               <label>Last Name</label>
                <div class="data"><input type="text" name="last_name" value="<?php echo $user[0]['last_name']; ?>"/></div>
            </div> 
            <div class="row">
               <label>First Name</label>
                <div class="data"><input type="text" name="first_name" value="<?php echo $user[0]['first_name']; ?>"/></div>
            </div> 
            <div class="row">
               <label>Username</label>
                <div class="data"><input type="text" name="username" value="<?php echo $user[0]['username']; ?>"/></div>
            </div> 
            <div class="row">
               <label>Password</label>
                <div class="data"><input type="text" name="password" value=""/></div>
            </div>     
            <div class="row">
               <label>Admin</label>
                <div class="data"><input type="checkbox" name="admin" value="1" <?php if ($user[0]['admin']==1) { echo "CHECKED"; } ?>/></div>
            </div>     
            <div class="row">
               <label>Full Access</label>
                <div class="data"><input type="checkbox" name="full" value="1" <?php if ($user[0]['full']==1) { echo "CHECKED"; } ?>/></div>
            </div>     
            <div class="row">
               <label>Read Access</label>
                <div class="data"><input type="checkbox" name="read" value="1" <?php if ($user[0]['read']==1) { echo "CHECKED"; } ?>/></div>
            </div>                 
            <div class="row">
               <label>Manage Data</label>
                <div class="data"><input type="checkbox" name="manage" value="1" <?php if ($user[0]['manage']==1) { echo "CHECKED"; } ?>/></div>
            </div>                 
            <div class="row">
               <label></label>
                <div class="data"><button>Save</button></div>
            </div>             
        </form>
    </div>
    <div style="float:left; width: 850px; padding-left: 15px;">
        <div class="sub-title">Current Print Devices</div>
        <div>
            <table>
                <thead>
                    <th width="75">Last Name</th>
                    <th width="75">First Name</th>
                    <th width="75">Username</th>
                    <th width="75">Email</th>
                    <th width="50">Active</th>
                    <th width="50">Admin</th>
                    <th width="50">Full Access</th>
                    <th width="50">Read Data</th>
                    <th width="50">Manage Data</th>
                    <th width="300">&nbsp;</th>
                </thead>
                <tbody>
                    <?php
                    $sql="SELECT * FROM users ORDER BY last_name";
                    $rs = $database->select($sql);                    
                    $total=0;
                    for ($x=0; $x < count($rs); $x++) {
                    ?>
                    <tr>
                        <td><?php echo $rs[$x]['last_name']; ?></td>
                        <td><?php echo $rs[$x]['first_name']; ?></td>
                        <td><?php echo $rs[$x]['username']; ?></td>
                        <td><?php echo $rs[$x]['email']; ?></td>                                                
                        <td><?php echo ($rs[$x]['active'] ? 'Yes' : 'No'); ?></td>
                        <td><?php echo ($rs[$x]['admin'] ? 'Yes' : 'No'); ?></td>
                        <td><?php echo ($rs[$x]['full'] ? 'Yes' : 'No'); ?></td>
                        <td><?php echo ($rs[$x]['read'] ? 'Yes' : 'No'); ?></td>
                        <td><?php echo ($rs[$x]['manage'] ? 'Yes' : 'No'); ?></td>                                               
                        <td><a href="/test"><button type="button" class="edit" id="<?php echo $rs[$x]['id']; ?>">Edit</button></a><button type="button" class="delete" id="<?php echo $rs[$x]['id']; ?>" >Remove</button></td>
                    </tr>
                    <?php 
                        $total = $total + 1;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="10"><?php echo $total; ?> user accounts</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>    
</div>
<script>
        $('.edit').click(function() {                
                document.location="<?php echo $_SERVER['PHP_SELF']; ?>?id=" + this.id;
        });     
        $('.delete').click(function() {           
            if (confirm("Do you want to delete this record?"))
            {	
                document.form.action.value="Delete";
                document.form.id.value=this.id;
                document.form.submit();                
            }
        });            
</script>
<?php
include("footer.php");
?>
