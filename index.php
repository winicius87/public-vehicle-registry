<?php
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    // Required field names
    $required = array('date', 'make', 'model', 'plate', 'expiration', 'description');
    
    // Loop over field names, make sure each one exists and is not empty
    $error = false;
    foreach($required as $field) {
      if (empty($_POST[$field])) {
        $error = true;
      }
    }
    if ( !$error ){
        $ip = $_SERVER['REMOTE_ADDR'];
        $_POST['ip']=$ip;
        $json_string = json_encode($_POST);
        /*
        Initial config of file is:
        {
        "data": [
            {"date":"aa","make":"bb","model":"cc","plate":"dd","expiration":"ee","description":"ff","ip":"gg"}
        ]}
        */
        
        $file_handle = fopen('public-vehicle-registry.json', 'r+');
        fseek ( $file_handle , -2, SEEK_END);
        fwrite($file_handle, ",\n");
        fwrite($file_handle, $json_string);
        fwrite($file_handle, "]}");
        fclose($file_handle);
        echo $json_string;
    }else{echo '';}
    return;
}
?>

<!doctype html>
<html>
<head>
    <title>Public Vehicle Registry</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" />
    <script src="//code.jquery.com/jquery-1.12.4.js"></script>
    <script src="//cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script>
    var t;
    $(document).ready(function() {
    t = $('#example').DataTable(
    {
        "lengthMenu": [ [-1, 10, 25, 50], ["All", 10, 25, 50] ],
        "ajax": "public-vehicle-registry.json",
        "columns": [
            { "data": "date" },
            { "data": "make" },
            { "data": "model" },
            { "data": "plate" },
            { "data": "expiration" },
            { "data": "description" }
        ]
    });
    var counter = 1;
 
    $('#addRow').on( 'click', function () {
        $.post( "index.php", $( "#testform" ).serialize() )
            .done(function(obj) {
                console.log(obj);
                if(obj==''){
                    alert( "All fields are required." );
                }else{
                    var data = JSON.parse(obj);
                    console.log(data.date);
                    var rowNode = t.row.add({ 
                        "date":data.date,
                        "make":data.make,
                        "model":data.model,
                        "plate":data.plate,
                        "expiration":data.expiration,
                        "description":data.description,
                     }).draw( false ).node();
                     
                    $( rowNode )
                    .css( 'color', 'red' )
                    .animate( { color: 'black' } );
                    $('#testform')[0].reset();
                    alert("Success.");
                }
            })
            .fail(function() {
                alert( "error" );
            });
        counter++;
    } );
 
    // Automatically add a first row of data
    //$('#addRow').click();
    } );
    </script>
</head>
<body>
<h1>Public Vehicle Registry</h1>
<button id="addRow">Add new row</button>
<form id="testform">
<table width="50%" cellspacing="0">
        <thead>
            <tr>
                <th>Date</th>
                <th>Make(s)</th>
                <th>Model(s)</th>
                <th>Plate(s)</th>
                <th>Expiration(s)</th>
                <th>Description(s)</th>
            </tr>
        </thead>
            <tr>
                <th><input size="12" type="text" name="date"/></th>
                <th><input size="12" type="text" name="make"/></th>
                <th><input size="12" type="text" name="model"/></th>
                <th><input size="12" type="text" name="plate"/></th>
                <th><input size="12" type="text" name="expiration"/></th>
                <th><input size="12" type="text" name="description"/></th>
            </tr>
        
</table>
</form>
<p><h2>I created this Public Vehicle Registry for anyone to use. All fields must be filled in, and the more detail that is written, then the easier it will be to confront the perpetrator. Users post anonymously, but IP addresses are collected to prevent spammers from filling the database with irrelevant entries. I hope with this system we are able to protect one another.
</h2><br/><h2>To download all the entries <a href="public-vehicle-registry.json" target="_blank">click here</a>.</h2></p>

<table id="example" class="display" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Date</th>
                <th>Make(s)</th>
                <th>Model(s)</th>
                <th>Plate(s)</th>
                <th>Expiration(s)</th>
                <th>Description(s)</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>Date</th>
                <th>Make(s)</th>
                <th>Model(s)</th>
                <th>Plate(s)</th>
                <th>Expiration(s)</th>
                <th>Description(s)</th>
            </tr>
        </tfoot>
    </table>
    <p><h3>This page is provided free of charge.
</h3></p>
</body>
</html>
