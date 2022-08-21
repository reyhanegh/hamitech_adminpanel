<? require_once "functions/database.php";

$database = new DB();
$connection = $database->connect();
$action = new Action();


// check admin access
if (!$action->admin()->access) {
    echo "<script type='text/javascript'>window.location.href = 'panel.php';</script>";
    return 0;
}

// ----------- urls ----------------------------------------------------------------------------------------------------
// main url for add, edit
$main_url = "post.php";
// main url for remove, change status
$list_url = "post-list.php";
// ----------- urls ----------------------------------------------------------------------------------------------------

// ----------- get data ------------------------------------------------------------------------------------------------
$counter = 1;
$result = $action->post_list();
// ----------- get data ------------------------------------------------------------------------------------------------

// ----------- delete --------------------------------------------------------------------------------------------------
if (isset($_GET['remove'])) {
    $id = $action->request('remove');
    $_SESSION['error'] = !$action->post_remove($id);
    header("Location: $list_url");
    return ;
}
// ----------- delete --------------------------------------------------------------------------------------------------

// ----------- change status -------------------------------------------------------------------------------------------
if (isset($_GET['status'])) {
    $id = $action->request('status');
    $_SESSION['error'] = !$action->admin_status($id);
    header("Location: $list_url");
    return ;
}
// ----------- change status -------------------------------------------------------------------------------------------

// ----------- check error ---------------------------------------------------------------------------------------------
$error = false;
if (isset($_SESSION['error'])) {
    $error = true;
    $error_val = $_SESSION['error'];
    unset($_SESSION['error']);
}
// ----------- check error ---------------------------------------------------------------------------------------------

// ----------- start html :) ------------------------------------------------------------------------------------------
include('header.php'); ?>

    <div class="page-wrapper">

        <div class="row page-titles">
            <!-- ----------- start breadcrumb ---------------------------------------------------------------------- -->
            <div class="col-md-12 align-self-center text-right">
                <h3 class="text-primary">پست ها</h3></div>
            <div class="col-md-12 align-self-center text-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="panel.php">
                            <i class="fa fa-dashboard"></i>
                            خانه
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">پست ها</a></li>
                </ol>
            </div>
            <!-- ----------- end breadcrumb ------------------------------------------------------------------------ -->

        </div>

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

            <!-- ----------- add button ---------------------------------------------------------------------------- -->
            <div class="row">
                <a class="add-user mb-2" href="<?= $main_url ?>">پست جدید<i class="fas fa-plus"></i></a>
            </div>
            <!-- ----------- add button ---------------------------------------------------------------------------- -->

            <?  $date = new DateTime('now');  ?>
            <!-- ----------- start row of table -------------------------------------------------------------------- -->
            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-body">

                            <div class="table-responsive m-t-5">

                                <table id="example23" class="display nowrap table table-hover table-striped"
                                       cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th class="text-center">ردیف</th>
                                        <th class="text-center">عنوان</th>
                                        <th class="text-center">توضیحات</th>
                                        <th class="text-center">نویسنده</th>
                                        <th class="text-center">دسته بندی</th>
                                        <th class="text-center">تاریخ انشار</th>
                                        <th class="text-center">آخرین ویرایش</th>
                                        <th class="text-center">مدیریت</th>
                                    </tr>
                                    </thead>

                                    <tbody class="text-center">
                                    <? while ($row = $result->fetch_object()) { ?>
                                        <tr class="text-center">

                                            <td class="text-center"><?= $counter++ ?></td>
                                            <td class="text-center"><?= $row->title ?></td>
                                            <td class="text-center"><?= $row->description ?></td>
                                            
                                            <td class="text-center"><? 
                                                $user=$action->get_data('tbl_admin',$row->author_id);
                                                if(isset($user->first_name)){echo $user->first_name.' '.$user->last_name;}else{echo "حساب حذف شده";}
                                                ?></td>
                                            <td class="text-center"><?$category = $action->get_data('tbl_cat',$row->cat_id);
                                            echo $category->name;?></td>
                                            <td class="text-center"><?= $action->time_to_shamsi( strtotime($row->created_at))?></td>
                                            <td class="text-center"><?= $action->time_to_shamsi(strtotime($row->modified_at))?></td>
                                           
                                            <td class="text-center">
                                                <a href="<?= $main_url ?>?edit=<?= $row->id ?>">
                                                    <i class="fa fa-pencil-square-o"></i>
                                                </a>
                                                |
                                                <a href="<?= $list_url ?>?remove=<?= $row->id ?>">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>







                                            

                                        </tr>
                                    <? } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ----------- end row of table ---------------------------------------------------------------------- -->

        </div>
    </div>

<? include('footer.php'); ?>