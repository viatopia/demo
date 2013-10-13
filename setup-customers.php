<?php
include("header.php");

$id = $_REQUEST['id'];
$action = $_POST['action'];
$page = $_REQUEST['page'];
if (!$page > 0) { $page=1; }

$sql="SELECT *, (SELECT COUNT(*) FROM customer_sources WHERE customer_id = customers.id) AS links FROM customers ORDER BY customer";
$rs = $database->select($sql);  
$customers = count($rs);
$rows = 50;
$pages = ceil($customers/$rows);
$start = ($page * $rows) - $rows;


if ($_POST) {
    switch($action) {
        case "Add" :
            $status = $database->insert("customers", $_POST);
            $id="";
            break;
        case "Update" :
            $status = $database->update("customers", $_POST, "id=:id");
            $id="";
            break;
        case "Delete" :
            $status =  $database->delete("customers", "id=$id");
            $id="";
            break;
    }
}

if ($id > 0) {
    $sql="SELECT * FROM users WHERE id=:id LIMIT 1";
    $parameters = Array("id"=>$id);
    $customer = $database->select($sql,$parameters);    
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
        <form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <input type="hidden" name="page" value="<?php echo $page; ?>">
            <input type="hidden" name="active" value="0">
            <input type="hidden" name="admin" value="0">
            <input type="hidden" name="full" value="0">
            <input type="hidden" name="read" value="0">
            <input type="hidden" name="manage" value="0">           
            <div class="row">
               <label>Customer Name</label>
                <div class="data"><input type="text" name="customer" value="<?php echo $user[0]['customer']; ?>" size="20" /></div>
            </div> 
            <div class="row">
               <label>Company Name</label>
                <div class="data"><input type="text" name="company" value="<?php echo $user[0]['company']; ?>" size="20"/></div>
            </div> 
            <div class="row">
               <label>Address</label>
                <div class="data"><input type="text" name="address" value="<?php echo $user[0]['address']; ?>" size="20"/></div>
            </div> 
            <div class="row">
               <label></label>
                <div class="data"><input type="text" name="address_2" value="<?php echo $user[0]['address_2']; ?>" size="20"/></div>
            </div> 
            <div class="row">
               <label>City</label>
                <div class="data"><input type="text" name="zip" value="<?php echo $user[0]['zip']; ?>" size="20"/></div>
            </div>             
            <div class="row">
               <label>State</label>
                <div class="data"><input type="text" name="state" value="<?php echo $user[0]['state']; ?>" size="2"/></div>
            </div>    
            <div class="row">
               <label>Zip</label>
                <div class="data"><input type="text" name="zip" value="<?php echo $user[0]['zip']; ?>" size="5"/></div>
            </div>             
            <div class="row">
               <label></label>
                <div class="data"><button>Save</button></div>
            </div>             
        </form>
    </div>
    <div style="float:left; width: 850px; padding-left: 15px;">
        <div class="sub-title">Current Print Devices</div>
        <div style="margin-bottom:10px;" align="right">
            Page 
            <?php
            for ($x=1; $x <= $pages; $x++) {
                if ($page == $x) {
                    echo $x . "&nbsp;";
                } else {
                    echo "<a href='?page=$x'>$x</a>&nbsp;";
                }
            }
            ?>
        </div>
        <div>
            <table>
                <thead>
                    <th width="150">Customer</th>
                    <th width="150">Company</th>
                    <th width="150">Address</th>
                    <th width="75"></th>
                    <th width="50">City</th>
                    <th width="50">State</th>
                    <th width="50">Zip</th>
                    <th width="50">Links</th>
                    <th width="300">&nbsp;</th>
                </thead>
                <tbody>
                    <?php
                    $sql="SELECT *, (SELECT COUNT(*) FROM customer_sources WHERE customer_id = customers.id) AS links FROM customers ORDER BY customer LIMIT $start, $rows";
                    $rs = $database->select($sql);                    
                    $total=0;
                    for ($x=0; $x < count($rs); $x++) {
                    ?>
                    <tr>
                        <td><?php echo $rs[$x]['customer']; ?></td>
                        <td><?php echo $rs[$x]['company']; ?></td>
                        <td><?php echo $rs[$x]['address']; ?></td>
                        <td><?php echo $rs[$x]['address_2']; ?></td>                                                
                        <td><?php echo $rs[$x]['city']; ?></td>                                                
                        <td><?php echo $rs[$x]['state']; ?></td>                                                
                        <td><?php echo $rs[$x]['zip']; ?></td>                                                
                        <td><?php echo $rs[$x]['links']; ?></td> 
                        <td><button type="button" class="links" id="<?php echo $rs[$x]['id']; ?>">Links</button><button type="button" class="edit" id="<?php echo $rs[$x]['id']; ?>">Edit</button><button type="button" class="delete" id="<?php echo $rs[$x]['id']; ?>" >Remove</button></td>
                    </tr>
                    <?php 
                        $total = $total + 1;
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="10"><?php echo $customers; ?> customer accounts</th>
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
        
        $('.links').click(function() {                
                document.location="/setup-customer-links.php?customer_id=" + this.id;
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
