<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly
//api url
define("JOBSLAH_APIURL", "https://api.jobslah.com/");
define("JOBSLAH_APICSS", "https://www.jobslah.com/widget/");

//check for reqired parameters
if (!isset($sanitized_args['employer']) || !is_numeric($sanitized_args['employer'])) {
    die('ID is missing form widget\'s parameters');
}
if (isset($sanitized_args['size'])) {
    $size = $sanitized_args['size'];
} else {
    $size = '400x600';
}
$wh = explode('x', $size);
if (isset($sanitized_args['limit']) && is_numeric($sanitized_args['limit'])) {
    $limit = $sanitized_args['limit'];
} else {
    $limit = 5;
}
$empID = $sanitized_args['employer'];
$style = isset($sanitized_args['style']) ? $sanitized_args['style'] : 0;

// curl request

$ch = curl_init();
$url = JOBSLAH_APIURL . 'jobs/?empid=' . $empID;
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //return the transfer as a string
$data = curl_exec($ch);
$data = json_decode($data);
curl_close($ch);     // close curl
?>

<?php
switch (strtoupper($style)) {
    case 'LIGHT':
        wp_enqueue_style('style', JOBSLAH_APICSS . 'style1.css');
        break;
    case 2:
        wp_enqueue_style('style', JOBSLAH_APICSS . 'style2.css');
        break;
    case 3:
        wp_enqueue_style('style', JOBSLAH_APICSS . 'style3.css');
        break;
    default:
	wp_enqueue_style('style', JOBSLAH_APICSS . 'main.css');
}

?>
<div id="jobslah-widget">
    <?php
    if (count($data) > $limit) {
        ?>
        <nav role="navigation">
            <ul class="cd-pagination no-space">
                <li class="button"><a id="myLink" title="Previous Page" href="#"
                                      onclick="jobslah_paginate('prev');return false;">Prev</a></li>
                <li class="jobslahcurpage">
                    1
                </li>
                <li class="button"><a id="myLink" title="Next Page" href="#"
                                      onclick="jobslah_paginate('next');return false;">Next</a>
                </li>
            </ul>
        </nav> <!-- cd-pagination-wrapper -->
        <?php
    }
    ?>

    <div class="jl-wrapper" id="joblist">
        <?php

        $page = 1;
        foreach ($data as $k => $row) {
            if ($page) {
                echo '<div id="page' . $page . '" class="pagination-pages">';
            }
            if (($k + 1) % $limit == 0) {
                $page = ($k + 1) / $limit + 1;
            } else {
                $page = false;
            }
            ?>
            <div class="jl-list clearfix">

                <a onclick="jobslah_showjob(<?php echo $row->id; ?>);return false;" target="_blank">
                    <div class="jl-numbers"><span><?php echo $k + 1; ?></span></div>
                    <div class="jl-title">
                        <h3><?php echo $row->title; ?></h3>

                        <div class="jl-desc">
                            <?php echo $row->desc ?>
                        </div>
                    </div>

                    <div class="jl-meta">
                        <div class="jl-date"><?php echo date('F j, Y', strtotime($row->date_created)); ?></div>
                    </div>
                </a>

            </div>
            <?php
            if ($page) {
                echo '</div>';
            }
        }
        if (!$page) {
            echo '</div>';
        }

        if (count($data) > $limit) {
            ?>
            <nav role="navigation">
                <ul class="cd-pagination no-space">
                    <li class="button"><a id="myLink" title="Previous Page" href="#"
                                          onclick="jobslah_paginate('prev');return false;">Prev</a></li>
                    <li class="jobslahcurpage">
                        1
                    </li>
                    <li class="button"><a id="myLink" title="Next Page" href="#"
                                          onclick="jobslah_paginate('next');return false;">Next</a>
                    </li>
                </ul>
            </nav> <!-- cd-pagination-wrapper -->
            <?php
        }
        ?>

    </div>

    <div id="loading" style="display: none; text-align: center">Loading...</div>

<!-- New details layout -->
  <div id="singlejob" style="display: none;">

  <div style="margin-top:10px;">  <a href="#" class="back-btn" id="buttonboxback" onclick="jobslah_backtolist();return false;">&lt; back</a><div class="pull-right"><a href="#" class="apply-btn" id="apply" target="_blank">&nbsp;APPLY&nbsp;<small>on jobslah.com</small></a></div></div>


    <h2 style="color:#4d4d4d;" id="singleTitle">title</h2>
      <!--<a href="" target="_blank" id="imglink"><img id="jobimg" src="" alt></a> -->
      <div><img id="jobimg" src="" alt></div>
    <span style="color:#999699;font-weight:bold;" >Full/Part Time:</span><br><p id="paytype"></p>
<br>
    <div class="container">
        <div class="right">
            <span style="color:#999699;font-weight:bold;" >Benefits:</span><br><p id="benefits"></p>
        </div>
        <div class="left">
            <span style="color:#999699;font-weight:bold;" >Requirements:</span><br><p id="requirements"></p>
        </div>
    </div>
<br>
    <div style="color:#999699;font-weight:bold;" >Description:</div><p id="singleDescription">description</p>

<br>
    <p id="address"></p>
    <a target="_blank" href="#" id="maplink">
        <img id="mapimg" src="" style="max-width: 480px">
    </a>
    <br>
  <div><a href="#"  class="apply-btn" id="apply" target="_blank">&nbsp;Apply&nbsp;<small>on jobslah.com</small></a></div>

</div>



    <script>
        var curpage = 1,
            width = '<?php echo $wh[0]; ?>',
            height = '<?php echo $wh[1]; ?>',
            paytype = ['All', 'Full Time', 'Part-time', 'Temp-Freelance'];
        if (width.search('%')) {
            width = '500';
        }

        function jobslah_showjob(key) {
            document.getElementById('joblist').style.display = 'none';
            document.getElementById('loading').style.display = 'block';
            var xhr = new XMLHttpRequest();


            xhr.open('GET', '<?php echo JOBSLAH_APIURL; ?>job?jobid=' + key);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('singlejob').style.display = 'block';
                    var data = JSON.parse(xhr.responseText);

                    document.getElementById('apply').href = 'https://www.jobslah.com/job/jobid/' + data[0].id;
                    //document.getElementById('imglink').href = 'https://www.jobslah.com/job/jobid/' + data[0].id;
                    if (data[0].job_image == null || data[0].job_image == '') {
                        document.getElementById('jobimg').src = '';
                    } else {
                        var tmp = new Image();
                        tmp.src = 'https://jobslahmedia.s3.amazonaws.com/' + data[0].job_image;
                        document.getElementById('jobimg').src = 'https://jobslahmedia.s3.amazonaws.com/' + data[0].job_image;
                    }
                    document.getElementById('singleTitle').innerHTML = data[0].title;
                    document.getElementById('paytype').innerHTML = paytype[data[0].type];
                    document.getElementById('singleDescription').innerHTML = data[0].desc;
                    document.getElementById('benefits').innerHTML = data[0].benefits;
                    document.getElementById('requirements').innerHTML = data[0].requirements;
                    document.getElementById('address').innerHTML = data[0].address;
                    document.getElementById('maplink').href = 'https://www.google.com.sg/maps/place/' + data[0].lat + ',' + data[0].long;
                    document.getElementById('mapimg').src = 'https://maps.googleapis.com/maps/api/staticmap?center=' + data[0].lat + ',' + data[0].long + '&zoom=15&size=' + document.getElementById('jobslah-widget').offsetWidth + 'x250&markers=color:blue|' + data[0].addr_lat + ',' + data[0].addr_long + '" alt="..." class="img-rounded img-responsive md-whiteframe-z5';

                }
                else {

                    document.getElementById('joblist').style.display = 'block';
                    alert('Request failed.');

                }
            };
            xhr.send();


        }
        function jobslah_backtolist() {
            document.getElementById('singlejob').style.display = 'none';
            document.getElementById('joblist').style.display = 'block';
        }
        function jobslah_paginate(direction) {
            if (direction == 'next') {
                var nextid = curpage + 1;
            } else {
                var nextid = curpage - 1;
            }
            var next = document.getElementById('page' + nextid);
            if (next) {
                document.getElementById('page' + curpage).style.display = 'none';
                next.style.display = 'block';
                if (direction == 'next') {
                    curpage++;
                } else {
                    curpage--;
                }
                document.getElementsByClassName('jobslahcurpage')[0].innerHTML = curpage;
                document.getElementsByClassName('jobslahcurpage')[1].innerHTML = curpage;
            }
        }
        (function () {
            var pages = document.getElementsByClassName('pagination-pages');
            pages[0].className += 'active';

        })();
    </script>

</div>
