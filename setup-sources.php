<?php
include("header.php");

$id = $_REQUEST['id'];
$action = $_POST['action'];

if ($_POST) {
    switch($action) {
        case "Add" :
            $status = $database->insert("sources", $_POST);
            $id="";
            break;
        case "Update" :
            $status = $database->update("sources", $_POST, "id=:id");
            $id="";
            break;
        case "Delete" :
            $status =  $database->delete("sources", "id=$id");
            $id="";
            break;
    }
}

if ($id > 0) {
    $sql="SELECT * FROM sources WHERE id=:id";
    $parameters = Array("id"=>$id);
    $rs = $database->select($sql,$parameters);    
    if (count($rs) > 0) {
        $source=$rs[0]["source"];
        $active=$rs[0]["active"];
    } else {
        $id="";
    }
}

if ($id > 0) { $action = "Update"; } else { $action = "Add"; }
?>
<div id="content">
    <?php if (strlen($status) > 0) { ?><div id="status"><?php echo $status; ?></div><?php } ?>
    <div id="title">Setup Data Sources</div>    
    <div style="float:left; width: 300px; height: 100%; position: relative;">
        <div class="sub-title"><?php echo $action; ?> Print Device</div>
        <form name="form" action="<?php echo $_SERVER['PHPSELF']; ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <input type="hidden" name="active" value="0">
            <div class="row">
               <label>Active</label>
                <div class="data"><input type="checkbox" name="active" value="1" <?php if ($active==1) { echo "CHECKED"; } ?>/></div>
            </div>             
            <div class="row">
               <label>Data Source Name</label>
                <div class="data"><input type="text" name="source" value="<?php echo $source; ?>"/></div>
            </div> 
            <div class="row">
               <label></label>
                <div class="data"><button>Save</button></div>
            </div>             
        </form>
    </div>
    <div style="float:left; width: 600px; padding-left: 15px;">
        <div class="sub-title">Current Data Sources</div>
        <div>
            <table>
                <thead>
                    <th width="150">Data Source</th>
                    <th width="75">Active</th>
                    <th width="150">&nbsp;</th>
                </thead>
                <tbody>
                    <?php
                    $sql="SELECT * FROM sources ORDER BY source";
                    $rs = $database->select($sql);                    
                    $total=0;
                    for ($x=0; $x < count($rs); $x++) {
                    ?>
                    <tr>
                        <td><?php echo $rs[$x]['source']; ?></td>
                        <td><?php echo ($rs[$x]['active'] ? 'Yes' : 'No'); ?></td>
                        <td><a href="/test"><button type="button" class="edit" id="<?php echo $rs[$x]['id']; ?>">Edit</button></a><button type="button" class="delete" id="<?php echo $rs[$x]['id']; ?>" >Remove</button></td>
                    </tr>
                    <?php 
                        $total = $total + 1;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th><?php echo $total; ?> data sources</th>
                        <th>&nbsp;</th>
                        <th>&nbsp;</th>
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
