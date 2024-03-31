<?php include '../include/config.inc.php'; ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Membros</title>    
    <link rel="stylesheet" href="../public/styles.css">            
</head>
<body class="bg-gray-100">

<div class="w-full max-w-xs mx-auto pt-10">
    <h2 class="font-bold text-lg mb-4 text-center">Adicionar membros ao curso</h2>

    <form method="post" action="<?php echo $arrConfig['url_modules'] . 'trata_adicionar_user_curso.mod.php' ?>" id="emailForm">
        <div id="email-list" class="mb-4">
            <!-- Os itens de e-mail serão inseridos aqui -->
        </div>
        <div class="flex mb-4 gap-3">
            <label class="input input-bordered flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 opacity-70"><path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" /><path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" /></svg>
                <input type="text" class="grow" id="email-input" placeholder="Email" />
            </label>
            
            <button type="button" id="add-email"
                    class="btn">
                Adicionar Email
            </button>
            <button type="submit"
                    class="btn btn-ghost">
                Submeter
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('email-input').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); 
            document.getElementById('add-email').click();
        }
    });

    document.getElementById('add-email').onclick = function() {
        var emailInput = document.getElementById('email-input');
        var emailList = document.getElementById('email-list');
        var email = emailInput.value.trim();
        
        // validar email
        var re = /\S+@\S+\.\S+/;
        if(!re.test(email)) {
            alert('Email inválido');
            return;
        }
        
        
        if(email) {
            var newDiv = document.createElement('div');
            newDiv.className = 'flex items-center bg-white px-4 py-2 rounded shadow mb-2';
            
            var newInput = document.createElement('input');
            newInput.type = 'hidden';
            newInput.name = 'emails[]';
            newInput.value = email;
            
            newDiv.innerHTML = `<span class="flex-auto">${email}</span>
                                <button type="button"
                                        onclick="removeEmail(this)"
                                        class="flex-none text-sm bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded">
                                    Remover
                                </button>`;
            newDiv.appendChild(newInput);                        
            emailList.appendChild(newDiv);                        
            emailInput.value = '';
        }
    };

    function removeEmail(button) {        
        button.parentElement.remove();
    }
</script>

</body>
</html>
