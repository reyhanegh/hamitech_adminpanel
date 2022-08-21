<? require_once "functions/database.php";
$database = new DB();
$connection = $database->connect();
$action = new Action();
// $author_id = null;
// ----------- urls ----------------------------------------------------------------------------------------------------
// main url for add , edit
$main_url = "post.php";
// main url for remove , change status
$list_url = "post-list.php";
// ----------- urls ----------------------------------------------------------------------------------------------------

// ----------- get data from database when action is edit --------------------------------------------------------------
$edit = false;
if (isset($_GET['edit'])) {
    $edit = true;
    $id = $action->request('edit');
    $result = $connection->query("SELECT * FROM tbl_post WHERE id ='$id'");
    if (!$action->result($result)) return false;
    if (!$result->num_rows) header("Location: post-list.php");
    $row = $result->fetch_object();


}
// ----------- get data from database when action is edit --------------------------------------------------------------

$result2 = $connection->query("SELECT * FROM tbl_cat");

// ----------- check error ---------------------------------------------------------------------------------------------
$error = false;
if (isset($_SESSION['error'])) {
    $error = true;
    $error_val = $_SESSION['error'];
    unset($_SESSION['error']);
}
// ----------- check error ---------------------------------------------------------------------------------------------
// echo "$_SESSION[admin_id]";

// ----------- add or edit ---------------------------------------------------------------------------------------------
if (isset($_POST['submit'])) {

    // get fields
    $title = $action->request('title');
    $description = $action->request('description');
    // if(!isset($author_id)){
    //     $author_id = $action->request('author_id');
    // }
    $author_id = $action->request('author_id');


    $cat_id = $action->request('cat');
    // $cat_id = $action->get_id('tbl_cat', $action->request('cat'))->id;

    // send query
    if ($edit) {
        $command = $action->post_edit($id, $title, $description, $author_id, $cat_id);
    } else {
        $command = $action->post_add($title, $description, $author_id, $cat_id);
    }

    // check errors
    if ($command) {
        $_SESSION['error'] = 0;
    } else {
        $_SESSION['error'] = 1;
    }

    // bye bye :)
    header("Location: $main_url?edit=$command");

}
// ----------- add or edit ---------------------------------------------------------------------------------------------

// ----------- start html :) ------------------------------------------------------------------------------------------
include('header.php'); ?>

<div class="page-wrapper">

    <div class="row page-titles">

        <!-- ----------- start title --------------------------------------------------------------------------- -->
        <div class="col-md-12 align-self-center text-right">
            <?php if (!isset($_GET['edit'])) { ?>
                <h3 class="text-primary">افزودن پست</h3>
            <?php } else { ?>
                <h3 class="text-primary">ویرایش پست</h3>
            <?php } ?>
        </div>
        <!-- ----------- end title ----------------------------------------------------------------------------- -->

        <!-- ----------- start breadcrumb ---------------------------------------------------------------------- -->
        <div class="col-md-12 align-self-center text-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="panel.php">
                        <i class="fa fa-dashboard"></i>
                        خانه
                    </a>
                </li>
                <li class="breadcrumb-item"><a href="post-list.php">پست ها</a></li>
                <?php if (!$edit) { ?>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">ایجاد</a></li>
                <?php } else { ?>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">ویرایش</a></li>
                <?php } ?>
            </ol>
        </div>
        <!-- ----------- end breadcrumb ------------------------------------------------------------------------ -->

    </div>

    <!-- ----------- start main container ---------------------------------------------------------------------- -->
    <div class="container-fluid">

        <!-- ----------- start error list ---------------------------------------------------------------------- -->
        <? if ($error) {
            if ($error_val) { ?>
                <div class="alert alert-danger">
                    عملیات ناموفق بود .
                </div>
            <? } else { ?>
                <div class="alert alert-info text-right">
                    عملیات موفق بود .
                </div>
            <? }
        } ?>
        <!-- ----------- end error list ------------------------------------------------------------------------ -->

        <div class="row">
            <div class="col-lg-6">

                <!-- ----------- start history ----------------------------------------------------------------- -->
                <? if ($edit) { ?>
                    <div class="row m-b-0">
                        <div class="col-lg-6">
                            <p class="text-right m-b-0">
                                تاریخ ثبت :
                                <?= $action->time_to_shamsi(strtotime($row->created_at))?>
                            </p>
                        </div>
                        <? if ($row->modified_at) { ?>
                            <div class="col-lg-6">
                                <p class="text-right m-b-0">
                                    آخرین ویرایش :
                                    <?= $action->time_to_shamsi(strtotime($row->modified_at))?>
                                </p>
                            </div>
                        <? } ?>
                    </div>
                <? } ?>
                <!-- ----------- end history ------------------------------------------------------------------- -->

                <!-- ----------- start row of fields ----------------------------------------------------------- -->
                <div class="card">
                    <div class="card-body">
                        <div class="basic-form">
                            <form action="" method="post" enctype="multipart/form-data">

                                <div class="form-group">
                                    <input type="text" name="title" class="form-control input-default "
                                           placeholder="عنوان"
                                           value="<?= ($edit) ? $row->title : "" ?>" required>
                                </div>

                                <div class="form-group">
                                 <textarea class="form-control input-default" type="text" name="description" rows="5"  placeholder="متن پست"
                                  required><?= ($edit) ? $row->description : "" ?></textarea>
                              
                                </div>

                                <div class="form-group">
                               <select class="form-select input-default form-control" name="cat" >
                                
                                    <? if($edit) { ?>
                                                <option selected value="<?= $row->cat_id  ?>" ><?= $action->get_data('tbl_cat',$row->cat_id)->name   ?></option>
                                                <? while ($row2 = $result2->fetch_object()) { ?>
                                                    <?if($row->cat_id != $row2->id) {?>  
                                                            <option value="<?= $row2->id ?>" >
                                                            <?= $row2->name ?>
                                                            </option>
                                                    <? } ?>
                                                <?}?>
                                        <? }
                                        else {?>
                                                <option selected disabled>دسته بندی</option>
                                                <?  while ($row2 = $result2->fetch_object()) { ?>
                                                    <option value="<?= $row2->id ?>" >
                                                    <?= $row2->name ?>
                                                    </option>
                                                <?}?>                                           
                                        <? } ?>
                                </select>
                               </div>

                               

                                <div class="form-actions">

                                    <label class="float-right">
                                        <input type="checkbox" class="float-right m-1" name="status" value="1"
                                            <? if ($edit && $row->status) echo "checked"; ?> >
                                        نمایش پست
                                    </label>

                                    <button type="submit" name="submit" class="btn btn-success sweet-success">
                                        <i class="fa fa-check"></i> ثبت
                                    </button>

                                    <a href="<?= $list_url ?>"><span name="back" class="btn btn-inverse">بازگشت به لیست</span></a>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <!-- ----------- end row of fields ----------------------------------------------------------- -->

            </div>
        </div>
    </div>
    <!-- ----------- end main container ------------------------------------------------------------------------ -->

</div>
<? include('footer.php'); ?>
// ----------- end html :) ---------------------------------------------------------------------------------------------

