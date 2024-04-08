<?php
include_once '../include/config.inc.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@event-calendar/build/event-calendar.min.css">
<script src="https://cdn.jsdelivr.net/npm/@event-calendar/build/event-calendar.min.js"></script>
<link rel="stylesheet" href="<?php echo $arrConfig['url_site'] . '/public/calendario.css' ?>">
<link rel="stylesheet" href="<?php echo $arrConfig['url_site'] . '/public/styles.css' ?>">

<?php
function gerar_calendario() {
    $html = '
    
    
    <div id="ec"></div>
    

    <script>

        let ec = new EventCalendar(document.getElementById(\'ec\'), {
            view: \'dayGridMonth\',
            allDaySlot: false,         
            
            events: [
                // your list of events
            ]
        });

    </script>
    
    ';
    return $html;
}
?>
<!-- <script>

let ec = new EventCalendar(document.getElementById('ec'), {
    view: 'dayGridMonth',
    allDaySlot: false,         
      
    events: [
        // your list of events
    ]
});

</script> -->


