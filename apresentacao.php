<?php
include 'include/config.inc.php';
include 'header.php';

if(!isset($_GET['op'])) {
    $_GET['op'] = 1;
}

?>

<div class="navbar bg-base-100">
    <div class="navbar-start">
        <a href="<?php echo $arrConfig['url_admin'] . 'index.php' ?>" class="btn btn-ghost text-2xl">educse</a>
    </div>
    <div class="navbar-center">
        <a href="<?php echo '?op=1'; ?>" class="btn btn-ghost text-sm">Inicio</a>    
        <a href="<?php echo '?op=2'; ?>" class="btn btn-ghost text-sm">Meio</a>    
        <a href="<?php echo '?op=3'; ?>" class="btn btn-ghost text-sm">Fim</a>    
    </div>
    <div class="navbar-end">   

        <div class="tooltip tooltip-left" data-tip="Indica o ano letivo da vista das turmas">
            
        </div>
        <!-- <button class="btn btn-ghost btn-circle">
            <div class="indicator">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span class="badge badge-xs badge-primary indicator-item"></span>
            </div> 
        </button> -->

        <div class="dropdown dropdown-end">
            <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                <div class="w-10 rounded-full">
                    <img alt="Tailwind CSS Navbar component" src="<?php echo $arrConfig['url_pfp'] . 'e.png'; ?>" />
                </div>
            </div>
            
                <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                    <!-- <li>
                    <a class="justify-between">
                        Profile
                        <span class="badge">New</span>
                    </a>
                    </li>                        
                    <li><a>Settings</a></li>
                    -->
                    <!-- <li><a href="' . $arrConfig['url_modules'] . 'trata_logout.mod.php' . '">Logout</a></li> -->
                </ul>
                
            </div>
        </div>
</div>
<div class="bg-primary flex justify-center w-full h-52 text-center">
    <b>
        <h1 class="bold mt-20 text-4xl">educse - apresentação</h1>        
    </b>
</div>

<?php 
switch($_GET['op']) {
    case '1':        
        ?>
        <div role="tablist" class="tabs tabs-lifted">
            <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="Tema" checked="checked" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                <div class="flex flex-col w-18">
                    <h1 class="text text-xl mb-4 font-bold">Escolha do tema</h1>
                    <span class="text">
                        
                    </span>
                </div>
            </div>            

            <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="Objetivos" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                Tab content 3
            </div>

            <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="Calendarização Inicial" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                Tab content 3
            </div>
        </div>
        <?php         
        break;
    case '2':
        ?>
        <div role="tablist" class="tabs tabs-lifted">
            <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="Tema" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                Tab content 1
            </div>

            <input
            type="radio"
            name="my_tabs_2"
            role="tab"
            class="tab"
            aria-label="Ferramentas utilizadas"
            checked="checked" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                Tab content 2
            </div>

            <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="Objetivos" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                Tab content 3
            </div>

            <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="Calendarização" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                Tab content 3
            </div>

            <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="Limitações" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                Tab content 3
            </div>

            <input type="radio" name="my_tabs_2" role="tab" class="tab" aria-label="Implementações futuras" />
            <div role="tabpanel" class="tab-content bg-base-100 border-base-300 rounded-box p-6">
                Tab content 3
            </div>
        </div>
        <?php
        break;
}
?>



