<?php

require_once('global.inc.php');
include('head.php');
include('menu.php');
?>

    <div class="container">
        <center><h1>WiFi networks</h1></center>
        <br/><br/>
        <a class="btn btn-default" id="scan" name="scan" href='/'>Scan for WiFi</a>
        <br><br>
        <table id="stats" class="tablesorter table table-striped table-hover stats">
            <thead>
            <tr>
                <th>SSID</th>
                <th>Good Signal</th>
                <th>Secured</th>
                <th>Connect</th>
            </tr>
            </thead>
            <tbody>



            <tr>

                <td><span class="text-success">Spond1</span></td>
                <td><i class="fa fa-signal fa-lg text-success"></i></td>


                <td><i class="fa fa-lock fa-lg text-success"></i></td>

                <td><i class="fa fa-check-square fa-lg text-success"></i></td>

            </tr>

            <tr>

                <td>Spond2</td>
                <td><i class="fa fa-ban fa-lg text-danger"></i></td>


                <td><i class="fa fa-lock fa-lg text-info"></i></td>

                <td><i class="fa fa-link fa-lg text-info"></i></td>

            </tr>

            <tr>

                <td>Spond3</td>
                <td><i class="fa fa-signal fa-lg text-info"></i></td>


                <td><i class="fa fa-unlock fa-lg text-danger"></i></td>

                <td><i class="fa fa-link fa-lg text-info"></i></td>

            </tr>

            <tr>

                <td>Spond4</td>
                <td><i class="fa fa-signal fa-lg text-info"></i></td>


                <td><i class="fa fa-lock fa-lg text-info"></i></td>

                <td><i class="fa fa-link fa-lg text-info"></i></td>

            </tr>

            </tbody>
        </table>
    </div>

<?php
include('foot.php');

