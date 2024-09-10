## **educse** 

**educse** é uma plataforma online para gestão de esforço estudantil e atividades escolares uma espécie de LMS, desenvolvido no âmbito da PAP (Prova de Aptidão Profissional)
utiliza tecnologias como PHP e seu universo de ferramentas além de contar com algumas bibliotecas em javascript e a implementação de uma funcionalidade de marcação de atividades a partir de um chat
utilizando de inteligência artificial consumindo a API da OpenAI

**

## setup 👨‍💻

**
Requisitos:
-   Node 18.9.0 ou superior
-   npm 6.14.15 ou superior
-   PHP 7.4 ou superior

O projeto tem uma estrutura simples. Siga o passo a passo para o começar:
~/

    npm install
   
~/ (dar build no styles.css para o tailwind importar as classes)

    npm run dev
   
   Após instalar as dependências do projeto, é necessário criar um ficheiro *.env*; O ficheiro deve ser criado na raiz do projeto, no mesmo nível dos diretórios associados ao backend. O modelo do ficheiro deve ser semelhante ao abaixo:
   
    LCSOPAI = 'Sua API KEY da OpenAI aqui'
    
    CHAVE_ENVIAR = 'Um valor aleatório de segurança'

**

também pode ser preciso ajustar os caminhos do $arrConfig['dir_site'] para se adequar a sua máquina.
