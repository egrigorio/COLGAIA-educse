<!DOCTYPE html>
<html data-theme="<?php echo isset($_SESSION['theme']) ? $_SESSION['theme'] : 'mytheme'; ?>" class="<?php echo (isset($_SESSION['cor']) ? 'bg-base-100' : 'bg-primary') ?> selection:bg-accent" lang="en" lang="en">
<?php 
if(isset($_SESSION['cor'])) {
    unset($_SESSION['cor']);
} 
include 'loading.php';

?>
<head>
    <?php setlocale(LC_TIME, 'pt_BR'); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>educse</title>
    <link rel="icon" type="image/x-icon" href="<?php echo $arrConfig['url_public'] . 'icone.ico'; ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@event-calendar/build/event-calendar.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@event-calendar/build/event-calendar.min.js"></script>
    <link rel="stylesheet" href="<?php echo $arrConfig['url_site'] . '/public/calendario.css' ?>">
    <link rel="stylesheet" href="<?php echo $arrConfig['url_site'] . '/public/styles.css' ?>">    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<script>
    
    $(document).ready(function(){
        
        var themes = ['nocas', 'mytheme', 'black'];
        var currentThemeIndex = localStorage.getItem('currentThemeIndex') || 0;

        $('input[type=radio][name=theme-radios]').change(function() {
            var theme = this.value;
            currentThemeIndex = themes.indexOf(theme);
            localStorage.setItem('currentThemeIndex', currentThemeIndex);
            $.ajax({
                url: '<?php echo $arrConfig['url_modules'] . 'trata_mudar_tema.mod.php' ?>',
                type: 'post',
                data: {theme: theme},
                success: function(response){
                    location.reload(); 
                }
            });
        });
        

        $(document).keydown(function(e) {        
            if(e.key == 'j' && e.metaKey) {
                
                e.preventDefault();
                setTheme();
                /* currentThemeIndex = (currentThemeIndex + 1) % themes.length;
                localStorage.setItem('currentThemeIndex', currentThemeIndex);
                var theme = themes[currentThemeIndex];
                $.ajax({
                    url: '<?php /* echo $arrConfig['url_modules'] . 'trata_mudar_tema.mod.php' */ ?>',
                    type: 'post',
                    data: {theme: theme},
                    success: function(response){
                        location.reload(); 
                    }
                }); */
            }
        });

        function setTheme() {
            
            currentThemeIndex = (currentThemeIndex + 1) % themes.length;
            localStorage.setItem('currentThemeIndex', currentThemeIndex);
            var theme = themes[currentThemeIndex];
            $.ajax({
                url: '<?php echo $arrConfig['url_modules'] . 'trata_mudar_tema.mod.php' ?>',
                type: 'post',
                data: {theme: theme},
                success: function(response){
                    location.reload(); 
                }
            });
        }
        $("#troca_tema").click(function(){
            setTheme();
        });

});    
</script>