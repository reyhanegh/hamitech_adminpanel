<? require_once "functions/database.php";
$database = new DB();
$connection = $database->connect();
$action = new Action();
// $author_id = null;
// ----------- urls ----------------------------------------------------------------------------------------------------
// main url for add , edit
$main_url = "report_user_signup.php";
// main url for remove , change status
// $list_url = "post-list.php";
// ----------- urls ----------------------------------------------------------------------------------------------------

// ----------- get data from database when action is edit --------------------------------------------------------------
$show = false;
if (isset($_GET['show'])) {
    $show = true;

    $from_date = $action->request('from_date');
    $from_date = strtotime($action->shamsi_to_miladi($from_date));

    $to_date_v2 = $action->request('to_date');
    $to_date = $action->request('to_date');
    $to_date = strtotime($action->shamsi_to_miladi_v2($to_date));

    $result = $connection->query("SELECT * FROM tbl_user WHERE `created_at` BETWEEN $from_date AND $to_date ORDER BY created_at");

    if (!$action->result($result)) return false;
    // if (!$result->num_rows) header("Location: post-list.php");
    $counter = 1;

}
// ----------- get data from database when action is edit --------------------------------------------------------------

// ----------- check error ---------------------------------------------------------------------------------------------
$error = false;
if (isset($_SESSION['error'])) {
    $error = true;
    $error_val = $_SESSION['error'];
    unset($_SESSION['error']);
}
// ----------- check error ---------------------------------------------------------------------------------------------

// ----------- add or edit ---------------------------------------------------------------------------------------------
if (isset($_POST['submit'])) {

    // get fields
    $from_date = $action->request('from_date');
    $to_date = $action->request('to_date');

    // bye bye :)
    header("Location: $main_url?show=&from_date=$from_date&to_date=$to_date");

}
// ----------- add or edit ---------------------------------------------------------------------------------------------

// ----------- start html :) ------------------------------------------------------------------------------------------
include('header.php'); ?>

<div class="page-wrapper">

    <div class="row page-titles">

        <!-- ----------- start title --------------------------------------------------------------------------- -->
        <div class="col-md-12 align-self-center text-right">
            <?php if (isset($_GET['show'])) { ?>
                <h3 class="text-primary">نمایش گزارش</h3>
            <?php } else { ?>
                <h3 class="text-primary">جستجو</h3>
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
                <li class="breadcrumb-item"><a href="post-list.php">گزارش ها</a></li>
                <?php if (!$show) { ?>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">تعریف</a></li>
                <?php } else { ?>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">نمایش</a></li>
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
                    <?= $to_date?>
                    <?= $from_date?>
                </div>
            <? } else { ?>
                <div class="alert alert-info text-right">
                    عملیات موفق بود .
                </div>
            <? }
        } ?>
        <!-- ----------- end error list ------------------------------------------------------------------------ -->

         <!-- <?= '1401/5/13 =>'.strtotime($action->shamsi_to_miladi('1401/5/13'))?>
        <br />
        <?= '1661058937 =>'.$action->miladi_to_shamsi(date('Y-m-d', '1661058937'))?>     -->
        
        <div class="row">
            <div class="col-lg-6">
                <!-- ----------- start row of fields ----------------------------------------------------------- -->
                <div class="card">
                <p class="card-title text-center">کاربران ثبت نام شده</p>

                    <div class="card-body">
                        <div class="basic-form">
                            <form action="" method="post" enctype="multipart/form-data">

                                <label class="float-right me-2">از تاریخ</label>
                                <input type="text" name="from_date" class="form-control input-default mb-3"
                                        placeholder=" روز/ماه/سال"
                                        value="<?= ($show) ? $action->miladi_to_shamsi(date('Y-m-d', $from_date)) : "" ?>" required>
                                    
                                <label class="float-right">تا تاریخ</label>     
                                <input type="text" name="to_date" class="form-control input-default "
                                placeholder=" روز/ماه/سال"
                                        value="<?= ($show) ? $action->miladi_to_shamsi_v2(date('Y-m-d', $to_date)) : "" ?>" required>
                
                                <div class="form-actions">

                                    <button type="submit" name="submit" class="btn btn-success sweet-success mt-3">
                                         نمایش
                                    </button>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <!-- ----------- end row of fields ----------------------------------------------------------- -->


            </div>
            <div class="col-lg-12">
            <?  if($show) { ?>
    <div class="card">
                    <div class="card-body">

                        <div class="table-responsive m-t-5">
                            <table id="example23" class="display nowrap table table-hover table-striped"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th class="text-center">ردیف</th>
                                    <th class="text-center">نام</th>
                                    <th class="text-center">نام خانوادگی</th>
                                    <th class="text-center">تاریخ ثبت نام</th>
                                    <th class="text-center">کدملی</th>
                                    <th class="text-center">وضعیت</th>
                                    <th class="text-center">مدیریت</th>
                                </tr>
                                </thead>

                                <tbody class="text-center">
                                <? while ($row = $result->fetch_object()) { ?>
                                    <tr class="text-center">

                                        <td class="text-center"><?= $counter++ ?></td>
                                        <td class="text-center"><?= $row->first_name ?></td>
                                        <td class="text-center"><?= $row->last_name ?></td>
                                        <td class="text-center"><?= $action->miladi_to_shamsi(date('Y-m-d', $row->created_at))  ?></td>
                                        <td class="text-center"><?= $row->national_code ?></td>

                                        <td class="text-center">
                                            <a href="<?= $list_url ?>?status=<?= $row->id ?>">
                                                <?
                                                if ($row->status) echo "<status-indicator positive pulse></status-indicator>";
                                                else echo "<status-indicator negative pulse></status-indicator>";
                                                ?>
                                            </a>
                                        </td>

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
<? } ?>
            </div>
        </div>
    </div>
    <!-- ----------- end main container ------------------------------------------------------------------------ -->

</div>
<? include('footer.php'); ?>
// ----------- end html :) ---------------------------------------------------------------------------------------------

