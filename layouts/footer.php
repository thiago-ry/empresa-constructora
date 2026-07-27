</div>


<?php


if(isset($script)){


    echo '<script src="/empresa_constructora/assets/js/'.$script.'.js"></script>';


}


?>


<script>


const menuToggle = document.getElementById("menuToggle");

const sidebar = document.querySelector(".sidebar");

const overlay = document.getElementById("sidebarOverlay");



if(menuToggle && sidebar && overlay){



    menuToggle.addEventListener("click",()=>{


        sidebar.classList.toggle("active");


        overlay.classList.toggle("active");



    });




    overlay.addEventListener("click",()=>{


        sidebar.classList.remove("active");


        overlay.classList.remove("active");



    });



}




</script>



</body>

</html>