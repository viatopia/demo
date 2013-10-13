<?php
include("header.php");

$id = $_REQUEST['id'];
$customer_id = $_REQUEST['customer_id'];
$action = $_POST['action'];

if ($_POST) {
    switch($action) {
        case "Add" :
            $status = $database->insert("customer_sources", $_POST);
            $id="";
            break;
        case "Update" :
            $status = $database->update("customer_sources", $_POST, "id=:id");
            $id="";
            break;
        case "Delete" :
            $status =  $database->delete("customer_sources", "id=$id");
            $id="";
            break;
    }
}

if ($customer_id > 0) {
    $customer = $database->select("SELECT * FROM customers WHERE id=:id", Array("id"=>$customer_id));
}

if ($id > 0) {
    $sql="SELECT * FROM customer_sources WHERE id=:id";
    $parameters = Array("id"=>$id);
    $customer_device = $database->select($sql,$parameters);    
}

if ($id > 0) { $action = "Update"; } else { $action = "Add"; }
?>
<div id="content">
    <?php if (strlen($status) > 0) { ?><div id="status"><?php echo $status; ?></div><?php } ?>
    <div id="title">Setup Customer Data Source Links</div>    
    <div style="float:left; width: 300px; height: 100%; position: relative;">
        <div class="sub-title"><?php echo $action; ?> Device Link</div>
        <form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
            <input type="hidden" name="action" value="<?php echo $action; ?>">            
            <div class="row">               
                <div class="data"><?php echo $customer[0]['customer'];?></div>
            </div>             
            <div class="row">&nbsp;</div>
            <div class="row">
               <label>Data Source</label>
                <div class="data">
                    <select name="source_id" style="width:140px;">
                        <option value="">-- select --</option>
                        <?php
                        $sources = $database->select("SELECT * FROM sources ORDER BY source");
                        for ($x=0; $x <= count($sources)-1; $x++) {
                            $selected="";
                            if ($customer_device[0]['source_id'] == $sources[$x]['id']) { $selected = "SELECTED"; }
                        ?>
                        <option value="<?php echo $sources[$x]['id'];?>" <?php echo $selected; ?>><?php echo $sources[$x]['source'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div> 
            <div class="row">
               <label>Customer ID</label>
                <div class="data"><input type="text" size="20" name="key" value="<?php echo $customer_device[0]['key']; ?>"/></div>
            </div>             
            <div class="row">
               <label></label>
                <div class="data"><button>Save</button></div>
            </div>             
        </form>
    </div>
    <div style="float:left; width: 600px; padding-left: 15px;">
        <div class="sub-title">Data Source Links</div>
        <div>
            <table>
                <thead>
                    <th width="150">Data Source</th>
                    <th width="75">Customer ID</th>
                    <th width="150">&nbsp;</th>
                </thead>
                <tbody>
                    <?php
                    $sql="SELECT customer_sources.*, sources.source FROM customer_sources 
                        LEFT OUTER JOIN sources ON customer_sources.source_id = sources.id
                        WHERE customer_sources.customer_id=$customer_id
                        ORDER BY source";                    
                    $rs = $database->select($sql);                    
                    $total=0;
                    for ($x=0; $x < count($rs); $x++) {
                    ?>
                    <tr>
                        <td><?php echo $rs[$x]['source']; ?></td>
                        <td><?php echo $rs[$x]['key']; ?></td>
                        <td><a href="/test"><button type="button" class="edit" id="<?php echo $rs[$x]['id']; ?>">Edit</button></a><button type="button" class="delete" id="<?php echo $rs[$x]['id']; ?>" >Remove</button></td>
                    </tr>
                    <?php 
                        $total = $total + 1;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th><?php echo $total; ?> links</th>
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
                document.location="<?php echo $_SERVER['PHP_SELF']; ?>?customer_id=<?php echo $customer_id; ?>&id=" + this.id;
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
