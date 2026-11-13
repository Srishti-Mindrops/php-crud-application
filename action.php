<?php 
 require_once "db.php";
 require_once "util.php";

 $db = new Database();
 $util = new Util();

 if(isset($_POST["add"])) {
    $fname = $util->testInput($_POST["fname"]);
    $lname = $util->testInput($_POST["lname"]);
    $email = $util->testInput($_POST["email"]);
    $phone = $util->testInput($_POST["phone"]);
    if($db->insert($fname, $lname, $email, $phone)) {
        echo $util->showMessage("success","User inserted successfully!");
    }else{
        echo $util->showMessage("warning","Something went wrong!");
    }
 }
if(isset($_GET["read"])){
    $users = $db->read();
    $output = '';
    if ($users){
        foreach($users as $row){
            $id = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
            $fname = htmlspecialchars($row['first_name'], ENT_QUOTES, 'UTF-8');
            $lname = htmlspecialchars($row['last_name'], ENT_QUOTES, 'UTF-8');
            $email = htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8');
            $phone = htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8');
            $time = htmlspecialchars($row['timestamp'] ?? '', ENT_QUOTES, 'UTF-8');
            $output .= '<tr>
                <td>'.$id.'</td>
                <td>'.$fname.'</td>
                <td>'.$lname.'</td>
                <td>'.$email.'</td>
                <td>'.$phone.'</td>
                <td>'.$time.'</td>
                <td><a href="#" id="'.$id.'" class="btn btn-success editlink" data-bs-toggle="modal"  data-bs-target="#editUserModal">Edit</a>
                    <a href="#" id="'.$id.'" class="btn btn-warning deletelink" >Delete</a></td>
            </tr>';
        }
        echo $output;
    }else{
        echo '<tr>
        <td colspan="7">No users found in the Database</td>
        </tr>';
    }
}
if(isset($_GET['edit'])){
    $id = (int)$_GET['id'];
    $user = $db->readOne($id);
    echo json_encode($user);
}
if(isset($_POST['update'])){
    $id = (int)$util->testInput($_POST['id']);
    $fname = $util->testInput($_POST['fname']);
    $lname = $util->testInput($_POST['lname']);
    $email = $util->testInput($_POST['email']);
    $phone = $util->testInput($_POST['phone']);
    if($db->update($id,$fname, $lname, $email, $phone)){
        echo $util->showMessage('success',"User updated successfully!");
    }else{
        echo $util->showMessage("warning","Something went wrong!");
    }
}
if(isset($_GET["delete"]) || isset($_POST["delete"])){
    $id = (int)($_POST["id"] ?? $_GET["id"]);
    if($db->delete($id)){
        echo $util->showMessage('danger',"User deleted successfully!");
    }else{
        echo $util->showMessage("warning","Something went wrong!");
    }
}
?>